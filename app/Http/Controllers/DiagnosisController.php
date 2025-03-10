<?php

namespace App\Http\Controllers;

use App\Mail\ReportMail;
use App\Models\Answer;
use App\Models\Client;
use App\Models\ClientAnswer;
use App\Models\EmailContent;
use App\Models\QuestionMultipleChoice; // Importe o modelo QuestionMultipleChoice
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class DiagnosisController extends Controller
{
    public function calculateDiagnosis($totalWeight)
    {
        // ... (código da função calculateDiagnosis - sem alterações) ...
        // (O código da função calculateDiagnosis permanece o mesmo)
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

    // Função auxiliar para buscar respostas SIMPLES (mantida, mas agora com nome mais claro)
    private function getSimpleClientAnswers($clientId, $submissionId)
    {
        return ClientAnswer::with(['question', 'answer'])
            ->where('client_id', $clientId)
            ->where('submission_id', $submissionId)
            ->whereNotNull('question_id') // Modificado para buscar apenas as que tem question_id
            ->get();
    }


    // Nova função para buscar respostas de MÚLTIPLA ESCOLHA
    private function getMultipleChoiceClientAnswers($clientId, $submissionId)
    {
        return ClientAnswer::with(['multipleChoiceAnswer.questionMultipleChoice']) // Eager load completo
        ->where('client_id', $clientId)
            ->where('submission_id', $submissionId)
            ->whereNull('question_id') // Busca apenas respostas de múltipla escolha (question_id é NULL)
            ->get();
    }

    public function generateReport($client_id, $submission_id)
    {
        // Busca respostas SIMPLES
        $simpleAnswers = $this->getSimpleClientAnswers($client_id, $submission_id);

        // Busca respostas de MÚLTIPLA ESCOLHA
        $multipleChoiceAnswers = $this->getMultipleChoiceClientAnswers($client_id, $submission_id);

        if ($simpleAnswers->isEmpty() && $multipleChoiceAnswers->isEmpty()) {
            return null; // Tratamento para relatório vazio
        }

        $allPoints = []; // Um único array

        // Adiciona PRIMEIRO as respostas SIMPLES
        foreach ($simpleAnswers as $clientAnswer) {
            $allPoints[] = [
                'question' => optional($clientAnswer->question)->question,
                'answer' => optional($clientAnswer->answer)->answer,
                'diagnosis_title' => optional($clientAnswer->question)->diagnosis_title,
                'diagnosis' => optional($clientAnswer->answer)->diagnosis,
                'solution' => optional($clientAnswer->answer)->solution,
                'strength_weakness_title' => optional($clientAnswer->answer)->strength_weakness_title,
                'strength_weakness' => optional($clientAnswer->answer)->strength_weakness,
                '_source' => 'simple', // Chave para diferenciar (ainda útil na view)
            ];
        }

        // Adiciona DEPOIS as respostas de MÚLTIPLA ESCOLHA
        foreach ($multipleChoiceAnswers as $clientAnswer) {
            $answer = $clientAnswer->multipleChoiceAnswer;  // Use o relacionamento!

            if ($answer) {
                $question = $answer->questionMultipleChoice;
                $allPoints[] = [
                    'question' => optional($question)->question_title,
                    'answer' => optional($answer)->answer,
                    'diagnosis_title' => optional($question)->solution_title,
                    'diagnosis' => optional($answer)->diagnosis,
                    'solution' => '',  // Sem solução
                    'strength_weakness_title' => optional($answer)->strength_weakness_title,
                    'strength_weakness' => optional($answer)->strength_weakness,
                    '_source' => 'multiple_choice', // Chave para diferenciar
                ];
            } else {
                \Log::warning("Resposta de múltipla escolha não encontrada para ClientAnswer ID: " . $clientAnswer->id);
            }

        }

        // Ordenação por 'diagnosis_title' (OPCIONAL, mas recomendado)
        usort($allPoints, function ($a, $b) {
            return strcmp($a['diagnosis_title'], $b['diagnosis_title']);
        });


        // Cálculo do peso total (mantido)
        $totalWeightSimple = $simpleAnswers->sum(function ($clientAnswer) {
            return $clientAnswer->answer ? $clientAnswer->answer->weight : 0;
        });

        $totalWeightMultipleChoice = $multipleChoiceAnswers->sum(function ($clientAnswer) {
            return $clientAnswer->multipleChoiceAnswer ? $clientAnswer->multipleChoiceAnswer->weight : 0; // Usar o relacionamento
        });

        $totalWeight = $totalWeightSimple + $totalWeightMultipleChoice;

        $diagnosisResult = $this->calculateDiagnosis($totalWeight);

        $fileName = 'diagnostico_' . $client_id . '_' . $submission_id . '.pdf';
        $pdfStorage = storage_path('app/public/' . $fileName);

        try {
            Pdf::view('reports.index', compact('totalWeight', 'diagnosisResult', 'allPoints'))
                ->save($pdfStorage);

            $this->sendReportByEmail($client_id, $submission_id, $pdfStorage);

            return response()->download($pdfStorage, $fileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error("Erro ao gerar ou enviar relatório para o cliente $client_id, submissão $submission_id: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->with('error', 'Erro ao gerar o relatório. Verifique os logs para mais detalhes.');
        }
    }

    // ... (código de sendReportByEmail - sem alterações) ...
    public function sendReportByEmail($client_id, $submission_id, $pdfStorage)
    {
        $client = Client::findOrFail($client_id);
        $emailContent = EmailContent::first(); // Você pode buscar configurações de e-mail do banco de dados

        if (!$emailContent) {
            \Log::error("Conteúdo do e-mail não encontrado ao tentar enviar relatório para o cliente $client_id.");
            return;
        }

        $fixedEmail = 'contato@bwolf.com.br'; // E-mail fixo para cópia

        Mail::to($client->email)
            ->cc($fixedEmail) // Adiciona o e-mail fixo em cópia
            ->send(new ReportMail($client, $pdfStorage, $emailContent)); // Passa $pdfStorage para o ReportMail
    }

    public function previewReport($client_id, $submission_id)
    {
        // Busca respostas SIMPLES
        $simpleAnswers = $this->getSimpleClientAnswers($client_id, $submission_id);

        // Busca respostas de MÚLTIPLA ESCOLHA
        $multipleChoiceAnswers = $this->getMultipleChoiceClientAnswers($client_id, $submission_id);

        if ($simpleAnswers->isEmpty() && $multipleChoiceAnswers->isEmpty()) {
            return view('reports.vazio');
        }

        $allPoints = []; // Um único array

        // Adiciona PRIMEIRO as respostas SIMPLES
        foreach ($simpleAnswers as $clientAnswer) {
            $allPoints[] = [
                'question' => optional($clientAnswer->question)->question,
                'answer' => optional($clientAnswer->answer)->answer,
                'diagnosis_title' => optional($clientAnswer->question)->diagnosis_title,
                'diagnosis' => optional($clientAnswer->answer)->diagnosis,
                'solution' => optional($clientAnswer->answer)->solution,
                'strength_weakness_title' => optional($clientAnswer->answer)->strength_weakness_title,
                'strength_weakness' => optional($clientAnswer->answer)->strength_weakness,
                '_source' => 'simple', // Chave para diferenciar (ainda útil na view)
            ];
        }

        // Adiciona DEPOIS as respostas de MÚLTIPLA ESCOLHA
        foreach ($multipleChoiceAnswers as $clientAnswer) {
            $answer = $clientAnswer->multipleChoiceAnswer;  // Use o relacionamento!

            if ($answer) {
                $question = $answer->questionMultipleChoice;
                $allPoints[] = [
                    'question' => optional($question)->question_title,
                    'answer' => optional($answer)->answer,
                    'diagnosis_title' => optional($question)->solution_title,
                    'diagnosis' => optional($answer)->diagnosis,
                    'solution' => '',  // Sem solução
                    'strength_weakness_title' => optional($answer)->strength_weakness_title,
                    'strength_weakness' => optional($answer)->strength_weakness,
                    '_source' => 'multiple_choice', // Chave para diferenciar
                ];
            } else {
                \Log::warning("Resposta de múltipla escolha não encontrada para ClientAnswer ID: " . $clientAnswer->id);
            }
        }

        // REMOVE A CHAMADA usort() !!!
        // usort($allPoints, function ($a, $b) { ... });  // REMOVA ISSO!

        // ... (resto do código - cálculo do peso, etc. - SEM ALTERAÇÕES) ...
        $totalWeightSimple = $simpleAnswers->sum(function ($clientAnswer) {
            return $clientAnswer->answer ? $clientAnswer->answer->weight : 0;
        });

        $totalWeightMultipleChoice = $multipleChoiceAnswers->sum(function ($clientAnswer) {
            return $clientAnswer->multipleChoiceAnswer ? $clientAnswer->multipleChoiceAnswer->weight : 0;
        });

        $totalWeight = $totalWeightSimple + $totalWeightMultipleChoice;

        $diagnosisResult = $this->calculateDiagnosis($totalWeight);
        $client = Client::findOrFail($client_id);

        // Passa $allPoints para a view
        return view('reports.index', compact('totalWeight', 'diagnosisResult', 'allPoints', 'client'));
    }
}
