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
        $multipleChoiceQuestions = QuestionMultipleChoice::with('answersMultipleChoices')->get(); // Nome do relacionamento corrigido

//        dd($multipleChoiceQuestions);
        return view('form', compact('questions', 'multipleChoiceQuestions'));
    }

    public function submitInfo(Request $request)
    {
        try {
            DB::beginTransaction();

            $answers = $request->input('answers', []); // Respostas simples
            $multipleChoiceAnswers = $request->input('multiple_choice_answers', []); // Respostas de múltipla escolha

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
                foreach ($answerIds as $answerId) {
                    ClientAnswer::updateOrCreate(
                        ['client_id' => $client->id, 'question_id' => $questionId, 'multiple_choice_answer_id' => $answerId],
                        ['answer_id' => null] // Ou algum valor padrão, se necessário
                    );
                }
            }

            DB::commit();

            $diagnosisController = new DiagnosisController();
            $diagnosisController->generateReport($client->id);

            return redirect('/thankyou')->with('success', 'Respostas enviadas com sucesso! O relatório foi enviado para seu e-mail.');
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Erro ao enviar respostas: ' . $e->getMessage());

            return redirect('/form')->with('error', 'Ocorreu um erro ao enviar as respostas. Tente novamente mais tarde.');
        }
    }

    public function submitForm(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|exists:answers,id',
            'multiple_choice_answers' => 'nullable|array',
            'multiple_choice_answers.*' => 'nullable|exists:answers_multiple_choices,id',
        ]);
    }
}
