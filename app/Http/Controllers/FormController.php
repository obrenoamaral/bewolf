<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientAnswer;
use App\Models\Question;
use Illuminate\Http\Request;

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
            // Decodifica as respostas do formulário
            $answers = json_decode($request->input('answers'), true);

            // Verifica se o cliente já existe
            $client = Client::where('email', $request->input('email'))->first();

            // Se não existir, cria um novo
            if (!$client) {
                $client = Client::create([
                    'name' => $request->input('name'),
                    'company' => $request->input('company'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                ]);
            }

            // Salva as respostas do cliente
            foreach ($answers as $questionId => $answerId) {
                ClientAnswer::updateOrCreate(
                    ['client_id' => $client->id, 'question_id' => $questionId],
                    ['answer_id' => $answerId]
                );
            }

            return redirect('/form')->with('success', 'Respostas enviadas com sucesso!');
        } catch (\Exception $e) {
            return redirect('/form')->with('error', 'Ocorreu um erro ao enviar as respostas.');
        }
    }



    public function submitForm(Request $request)
    {
        // Valida as respostas do formulário
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|exists:answers,id', // Verifica se as respostas existem na tabela `answers`
        ]);

        // Armazena as respostas na sessão para uso posterior no popup
        session(['answers' => $request->input('answers')]);

    }
}
