<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientAnswer;
use App\Models\Question;
use App\Models\QuestionMultipleChoice;
use App\Models\AnswersMultipleChoice; // Certifique-se de que este modelo está importado
use Illuminate\Http\Request;
use App\Http\Controllers\DiagnosisController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FormController extends Controller
{
    public function showForm()
    {
        $questions = Question::with('answers')->get();
        $multipleChoiceQuestions = QuestionMultipleChoice::with('answersMultipleChoice')->get();

        return view('form', compact('questions', 'multipleChoiceQuestions'));
    }

    public function submitInfo(Request $request)
    {
        try {
            DB::beginTransaction();

            // Respostas simples
            $answers = $request->input('answers', []);

            // Respostas de múltipla escolha
            $multipleChoiceAnswers = $request->input('multiple_choice_answers', []);

            // Recuperar ou criar o cliente
            $client = Client::where('email', $request->input('email'))->first();

            if (!$client) {
                $client = Client::create([
                    'name' => $request->input('name'),
                    'company' => $request->input('company'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                ]);
            }

            // Gerar um UUID para a submissão
            $submissionId = Str::uuid()->toString();

            // Salvar respostas simples
            foreach ($answers as $questionId => $answerId) {
                // Garantir que answer_id seja um inteiro
                $answerId = is_array($answerId) ? (int) $answerId[0] : (int) $answerId;

                ClientAnswer::create([
                    'client_id' => $client->id,
                    'question_id' => $questionId,
                    'answer_id' => $answerId,
                    'submission_id' => $submissionId, // Adicionando o UUID
                ]);
            }

            // Salvar respostas de múltipla escolha
            foreach ($multipleChoiceAnswers as $questionId => $answerIds) {
                // Se $answerIds não for um array (caso apenas uma opção seja selecionada),
                // transforme-o em um array.
                $answerIds = is_array($answerIds) ? $answerIds : [$answerIds];

                foreach ($answerIds as $answerId) {
                    // Certifique-se de que $answerId seja um inteiro.
                    $answerId = (int) $answerId;

                    // Crie o registro em ClientAnswer.
                    ClientAnswer::create([
                        'client_id' => $client->id,
                        'submission_id' => $submissionId,
                        'question_multiple_choices_id' => $questionId, // ID da pergunta de múltipla escolha
                        'multiple_choice_answer_id' => $answerId,     // ID da resposta de múltipla escolha selecionada
                        // question_id e answer_id devem ser NULL para respostas de múltipla escolha.
                        'question_id' => null,
                        'answer_id' => null,
                    ]);
                }
            }

            DB::commit();

            // Geração do relatório.  CORREÇÃO AQUI:
            $diagnosisController = new DiagnosisController();
            $report = $diagnosisController->generateReport($client->id, $submissionId); // Passa $submissionId

            if (!$report) {
                throw new \Exception('Falha ao gerar o relatório.');
            }

            return response()->json([
                'success' => true,
                'message' => 'Respostas enviadas com sucesso!',
                'redirect' => url('/thankyou')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Erro ao enviar respostas: ' . $e->getMessage());

            return redirect('/')->with('error', 'Ocorreu um erro ao enviar as respostas. Tente novamente mais tarde.');
        }
    }
}
