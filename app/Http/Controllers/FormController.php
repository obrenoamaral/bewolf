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
        $questions = \App\Models\Question::with('answers')->get();
        $multipleChoiceQuestions = \App\Models\QuestionMultipleChoice::with('answersMultipleChoice')->get();

        return view('form', compact('questions', 'multipleChoiceQuestions'));
    }

    public function submitInfo(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'answers' => 'required|array',
            'answers.*' => 'required|exists:answers,id',
            'multiple_choice_answers' => 'nullable|array',
            'multiple_choice_answers.*' => 'nullable|array',
            'multiple_choice_answers.*.*' => 'nullable|exists:answers_multiple_choices,id',
        ]);

        try {
            DB::beginTransaction();

            $client = Client::where('email', $request->input('email'))->first();
            if (!$client) {
                $client = Client::create([
                    'name' => $request->input('name'),
                    'company' => $request->input('company'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                ]);
            }

            $answers = $request->input('answers', []);
            foreach ($answers as $questionId => $answerId) {
                if ($answerId !== null) {
                    ClientAnswer::create([
                        'client_id' => $client->id,
                        'question_id' => $questionId,
                        'answer_id' => $answerId,
                        'multiple_choice_answer_id' => null, // Garante que este campo seja nulo para respostas simples
                    ]);
                }
            }

            $multipleChoiceAnswers = $request->input('multiple_choice_answers', []);
            foreach ($multipleChoiceAnswers as $questionMultipleChoiceId => $answerIds) {
                if (is_array($answerIds)) {
                    foreach ($answerIds as $answerMultipleChoiceId) {
                        ClientAnswer::create([
                            'client_id' => $client->id,
                            'question_id' => $questionMultipleChoiceId,
                            'answer_id' => null, // Garante que este campo seja nulo para respostas múltiplas
                            'multiple_choice_answer_id' => $answerMultipleChoiceId,
                        ]);
                    }
                }
            }

            DB::commit();

            $diagnosisController = new DiagnosisController();
            $diagnosisController->generateReport($client->id);

            return redirect('/thankyou')->with('success', 'Respostas enviadas com sucesso! O relatório foi enviado para seu e-mail.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            \Log::error('Erro no banco de dados ao enviar respostas: ' . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro ao salvar as respostas. Tente novamente mais tarde.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao enviar respostas: ' . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro ao enviar as respostas. Tente novamente mais tarde.');
        }
    }


    public function submitForm(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|exists:answers,id',  // Valida que cada resposta simples existe
            'multiple_choice_answers' => 'nullable|array',
            'multiple_choice_answers.*' => 'nullable|array', // Permite que múltiplas respostas sejam enviadas
            'multiple_choice_answers.*.*' => 'nullable|exists:answers_multiple_choices,id', // Valida que as respostas de múltipla escolha existem
        ]);
    }

}
