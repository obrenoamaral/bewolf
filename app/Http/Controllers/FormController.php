<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientAnswer;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\DiagnosisController; // Correct

class FormController extends Controller
{
    public function showForm()
    {
        $questions = Question::with('answers')->get();

        return view('form', compact('questions'));
    }

    public function submitInfo(Request $request)
    {
        try {
            $answers = json_decode($request->input('answers'), true);

            $client = Client::where('email', $request->input('email'))->first();

            if (!$client) {
                $client = Client::create([
                    'name' => $request->input('name'),
                    'company' => $request->input('company'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                ]);
            }

            foreach ($answers as $questionId => $answerId) {
                ClientAnswer::updateOrCreate(
                    ['client_id' => $client->id, 'question_id' => $questionId],
                    ['answer_id' => $answerId]
                );
            }

            $diagnosisController = new DiagnosisController();
            $diagnosisController->generateReport($client->id);

            return redirect('/thankyou')->with('success', 'Respostas enviadas com sucesso! O relatório foi enviado para seu e-mail.');
        } catch (\Exception $e) {
            return redirect('/form')->with('error', 'Ocorreu um erro ao enviar as respostas.');
        }
    }

    public function submitForm(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|exists:answers,id',
        ]);

        session(['answers' => $request->input('answers')]);

    }
}
