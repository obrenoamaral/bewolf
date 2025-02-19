<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientAnswer;
use App\Models\Question;
use App\Models\QuestionMultipleChoice;
use Illuminate\Http\Request;
use App\Http\Controllers\DiagnosisController;
use Illuminate\Support\Facades\DB;

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

            // Salvar respostas simples
            foreach ($answers as $questionId => $answerId) {
                ClientAnswer::updateOrCreate(
                    ['client_id' => $client->id, 'question_id' => $questionId],
                    ['answer_id' => $answerId]
                );
            }

            // Salvar respostas de múltipla escolha
            foreach ($multipleChoiceAnswers as $questionId => $answerIds) {
                if (is_array($answerIds)) {
                    foreach ($answerIds as $answerId) {
                        ClientAnswer::updateOrCreate(
                            [
                                'client_id' => $client->id,
                                'question_multiple_choices_id' => $questionId,
                                'multiple_choice_answer_id' => $answerId,
                            ],
                            ['answer_id' => null]
                        );
                    }
                } else {
                    \Log::warning("Resposta de múltipla escolha inválida: questionId = {$questionId}, answerIds = " . print_r($answerIds, true));
                }
            }

            DB::commit();

            // Geração do relatório
            $diagnosisController = new DiagnosisController();
            $report = $diagnosisController->generateReport($client->id);

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
