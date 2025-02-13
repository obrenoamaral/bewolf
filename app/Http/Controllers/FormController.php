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

//        dd($multipleChoiceQuestions);
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
                foreach ($answerIds as $answerId) {
                    ClientAnswer::updateOrCreate(
                        ['client_id' => $client->id, 'question_id' => $questionId, 'multiple_choice_answer_id' => $answerId],
                        ['answer_id' => null] // Valor padrão, se necessário
                    );
                }
            }

            DB::commit();

            // Geração do relatório
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
            'answers.*' => 'required|exists:answers,id',  // Valida que cada resposta simples existe
            'multiple_choice_answers' => 'nullable|array',
            'multiple_choice_answers.*' => 'nullable|array', // Permite que múltiplas respostas sejam enviadas
            'multiple_choice_answers.*.*' => 'nullable|exists:answers_multiple_choices,id', // Valida que as respostas de múltipla escolha existem
        ]);
    }

}
