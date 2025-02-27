<?php

namespace App\Http\Controllers;

use App\Mail\ReportMail;
use App\Models\Answer;
use App\Models\Client;
use App\Models\ClientAnswer;
use App\Models\EmailContent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf; // Importe a facade correta


class DiagnosisController extends Controller
{

    public function calculateDiagnosis($totalWeight)
    {
        $diagnoses = [
            'empreendedorExtintor' => [

                'name' => 'Empreendedor Extintor',

                'description' => 'Perfil Empreendedor em Risco Alto de Resultados Negativos, está em uma situação crítica e precisa de uma intervenção urgente nos 3 pilares fundamentais do negócio: Estratégia, Pessoas e Processos. Afinal, ainda não tem direcionamento efetivo de sua operação, e é provável que esteja enfrentando um caos operacional. Os processos são indefinidos, o fluxo de caixa pessoal e da empresa estão misturados, e há dificuldade em delegar e alinhar os resultados com a equipe.



Esse cenário leva o empresário a trabalhar mais de 12 horas por dia, com baixo tempo de qualidade para si e para sua família, além da incapacidade de se ausentar para tirar férias. O nível de estresse é elevado, com uma equipe desmotivada e uma cultura organizacional reativa, onde todos os dias surgem crises e problemas inesperados. O time, dependente de decisões constantes do empresário, vê tudo como urgente, e ele acaba sendo consumido pela necessidade de "apagar incêndios" em vez de focar no crescimento e na melhoria contínua do negócio.'

            ],

            'empreendedorSobrecarregado' => [

                'name' => 'Empreendedor Sobrecarregado',

                'description' => 'Perfil Empreendedor Tradicional com Baixa Abertura para Inovação - Este perfil indica um empreendedor mais Tradicional, com um ambiente de baixo incentivo à inovação e uma grande centralização das atividades. Frequentemente, o time não consegue resolver questões importantes sem a presença direta da liderança, o que resulta em uma baixa autonomia e dependência dos poucos funcionários capacitados. Os processos da empresa ainda são pouco organizados, o que compromete a eficiência operacional.



É crucial que este empresário inicie uma estruturação estratégica mais robusta, formalizando 100% dos processos da empresa e estabelecendo ciclos regulares de acompanhamento de metas e indicadores. Além disso, é essencial implementar feedbacks constantes, realizar avaliações institucionais, como pesquisas 360° e de clima organizacional, e trabalhar para reduzir a "donodependência" — ou seja, a excessiva dependência dos sócios e líderes para a execução das tarefas cotidianas.'

            ],

            'empreendedorVencedor' => [

                'name' => 'Empreendedor Mentalmente Vencedor',

                'description' => 'Perfil Empreendedor com Mentalidade Vencedora - Este perfil descreve um empreendedor com uma mentalidade vencedora, que compreende a importância de inovar constantemente e de buscar sempre novos conhecimentos. Ele entende que o desenvolvimento e a estruturação do negócio são processos contínuos, nos quais as estratégias e processos devem ser constantemente revisados e ajustados. Além disso, sabe que o desenvolvimento do time é essencial, e que todos devem estar alinhados com a visão, missão e valores da empresa.



Os principais desafios enfrentados por esse empreendedor envolvem a validação da cultura organizacional, o crescimento e a expansão da estrutura do negócio, e o alinhamento constante entre estratégia, pessoas e processos. O foco deste empreendedor deve ser reduzir desperdícios de produtividade e aumentar o engajamento do time, garantindo que as metas sejam claras e bem definidas para todos.'

            ],

        ];

        if ($totalWeight <= 68) {
            return $diagnoses['empreendedorExtintor'];
        } elseif ($totalWeight >= 69 && $totalWeight <= 88) {
            return $diagnoses['empreendedorSobrecarregado'];
        } else {
            return $diagnoses['empreendedorVencedor'];
        }
    }

    public function generateReport($client_id)
    {
        $clientAnswers = ClientAnswer::where('client_id', $client_id)
            ->with(['answer', 'question'])
            ->get();


        $multipleChoiceAnswers = ClientAnswer::where('client_id', $client_id)
            ->whereHas('questionMultipleChoice')
            ->with(['questionMultipleChoice', 'answerMultipleChoice'])
            ->get();


        if ($clientAnswers->isEmpty() && $multipleChoiceAnswers->isEmpty()) {
            return response()->json(['message' => 'No answers found for this client.'], 404);
        }

        $orderedPoints = [];

        foreach ($clientAnswers as $clientAnswer) {
            if (!$clientAnswer->answer || !$clientAnswer->question) {
                continue;
            }

            $orderedPoints[] = [
                'question' => $clientAnswer->question->question,
                'diagnosis_title' => $clientAnswer->question->diagnosis_title,
                'diagnosis' => $clientAnswer->answer->diagnosis,
                'solution' => $clientAnswer->answer->solution,
                'answer' => $clientAnswer->answer->answer,
                'strength_weakness_title' => $clientAnswer->answer->strength_weakness_title,
                'strength_weakness' => $clientAnswer->answer->strength_weakness,
            ];
        }

        $reportData = $clientAnswers->map(function ($clientAnswer) {
            if (!$clientAnswer->answer || !$clientAnswer->question) {
                return null;
            }
            return [
                'question' => $clientAnswer->question->question,
                'diagnosis_title' => $clientAnswer->question->diagnosis_title,
                'diagnosis' => $clientAnswer->answer->diagnosis,
                'solution' => $clientAnswer->answer->solution,
                'answer' => $clientAnswer->answer->answer,
            ];
        })->filter();


        // Calcular o peso total, incluindo as respostas de múltipla escolha
        $totalWeight = $clientAnswers->sum(function ($clientAnswer) {
            return $clientAnswer->answer ? $clientAnswer->answer->weight : 0;
        });

        //Soma o peso das multiplas escolhas
        $totalWeight += $multipleChoiceAnswers->sum(function ($multipleChoiceAnswer) {
            return $multipleChoiceAnswer->answerMultipleChoice ? $multipleChoiceAnswer->answerMultipleChoice->weight : 0;
        });

        if ($multipleChoiceAnswers->isEmpty()) {
            \Log::warning('No multiple choice answers found for client: ' . $client_id);
        }


        $diagnosisResult = $this->calculateDiagnosis($totalWeight);

        $fileName = 'diagnostico_' . $client_id . '.pdf';
        $pdfStorage = storage_path('app/public/' . $fileName); // Use $fileName aqui

        try {
            Pdf::view('reports.index', compact('totalWeight', 'diagnosisResult', 'orderedPoints', 'multipleChoiceAnswers', 'reportData'))
                ->save($pdfStorage); // Salva o PDF

            // Opcional: Enviar por e-mail (descomente se quiser enviar)
            $this->sendReportByEmail($client_id, $pdfStorage);

            return response()->download($pdfStorage, $fileName)->deleteFileAfterSend(true); // Retorna o PDF e o exclui depois

        } catch (\Exception $e) {
            \Log::error('Erro ao gerar ou enviar relatório: ' . $e->getMessage());
            return back()->with('error', 'Erro ao gerar o relatório. Tente novamente mais tarde.');
        }

    }

    public function sendReportByEmail($client_id, $pdfStorage)
    {
        $client = Client::findOrFail($client_id);
        $emailContent = EmailContent::first();

        $fixedEmail = 'contato@bwolf.com.br';

        Mail::to($client->email)
            ->cc($fixedEmail)
            ->send(new ReportMail($client, $pdfStorage, $emailContent));
    }
}
