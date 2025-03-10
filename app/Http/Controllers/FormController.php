<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientAnswer;
use App\Models\Question;
use App\Models\QuestionMultipleChoice;
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

            // 1. GERAR O SUBMISSION_ID
            $submissionId = Str::uuid()->toString();

            // 2. Salvar respostas simples
            foreach ($answers as $questionId => $answerId) {
                $answerId = is_array($answerId) ? (int) $answerId[0] : (int) $answerId;

                ClientAnswer::create([ // Usar create, pois cada resposta é única por submissão
                    'client_id' => $client->id,
                    'question_id' => $questionId,
                    'answer_id' => $answerId,
                    'submission_id' => $submissionId, // ADICIONADO!
                    'question_type' => 'normal', // ADICIONADO!
                ]);
            }

            // 3. Salvar respostas de múltipla escolha (CORRIGIDO)
            foreach ($multipleChoiceAnswers as $questionId => $answerId) {

                ClientAnswer::create([  // Usar create, pois cada resposta é única por submissão
                    'client_id' => $client->id,
                    'question_id' => $questionId,
                    'answer_id' => $answerId, // Usar $answerId diretamente
                    'submission_id' => $submissionId, // ADICIONADO!
                    'question_type' => 'multiple_choice', // ADICIONADO!
                ]);
            }

            DB::commit();

            // 4. Obtenha o submissionId MAIS RECENTE aqui, logo após salvar as respostas:
            $lastSubmissionId = ClientAnswer::where('client_id', $client->id)
                ->orderBy('created_at', 'desc')
                ->value('submission_id');


            // Geração do relatório (USANDO A OPÇÃO 1 - Passando o submission_id)
            $diagnosisController = new DiagnosisController();
            $report = $diagnosisController->generateReport($client->id, $lastSubmissionId); // Passa o submissionId

            if ($report === null) { //Verifica explicitamente se é null, para o caso da view de vazio
                return view('reports.vazio');
            }

            if (!$report) {
                throw new \Exception('Falha ao gerar o relatório.'); // Lança uma exceção
            }


            return response()->json([
                'success' => true,
                'message' => 'Respostas enviadas com sucesso!',
                'redirect' => url('/thankyou')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Erro ao enviar respostas: ' . $e->getMessage() . "\n" . $e->getTraceAsString()); // Log MAIS detalhado

            // Retorna uma resposta JSON em caso de erro, para manter a consistência com AJAX
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao enviar as respostas. Tente novamente mais tarde.'
            ], 500); // 500 Internal Server Error
        }
    }
}
