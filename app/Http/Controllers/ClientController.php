<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientAnswer;
use App\Models\QuestionMultipleChoice; // Importe o model de múltipla escolha
use App\Models\AnswersMultipleChoice; // Importe o model de respostas de múltipla escolha
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        return view('clients.index', compact('clients'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'company' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'answers' => 'required|array',
                'answers.*.question_id' => 'required|exists:questions,id',
                'answers.*.answer_id' => 'required|exists:answers,id',

                // Validação para as respostas de múltipla escolha:
                'multiple_choice_answers' => 'nullable|array', // Campo opcional, pois nem todos terão respostas de múltipla escolha
                'multiple_choice_answers.*.question_id' => 'required|exists:question_multiple_choices,id',
                'multiple_choice_answers.*.answer_id' => 'required|exists:answers_multiple_choices,id',
            ]);


            $client = Client::create([
                'name' => $validated['name'],
                'company' => $validated['company'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);

            // Salva as respostas normais
            foreach ($validated['answers'] as $answer) {
                ClientAnswer::create([
                    'client_id' => $client->id,
                    'question_id' => $answer['question_id'],
                    'answer_id' => $answer['answer_id'],
                ]);
            }

            // Salva as respostas de múltipla escolha (se houver)
            if (isset($validated['multiple_choice_answers'])) {
                foreach ($validated['multiple_choice_answers'] as $answer) {
                    ClientAnswer::create([
                        'client_id' => $client->id,
                        'question_id' => $answer['question_id'],
                        'answer_id' => $answer['answer_id'],
                    ]);
                }
            }


            DB::commit();

            return redirect()->back()->with('success', 'Informações salvas com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Erro ao salvar informações: ' . $e->getMessage());
        }
    }
}
