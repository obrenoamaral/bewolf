<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientAnswer;
use App\Models\QuestionMultipleChoice; // Importe o model de múltipla escolha
use App\Models\AnswersMultipleChoice; // Importe o model de respostas de múltipla escolha
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule; // Importante para validação unique

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
                'multiple_choice_answers' => 'nullable|array',
                'multiple_choice_answers.*.question_id' => [
                    'required',
                    'exists:question_multiple_choices,id',
                ],
                'multiple_choice_answers.*.answer_id' => [
                    'required',
                    'exists:answers_multiple_choices,id',
                ],

            ]);

            // 1. Encontrar ou criar o cliente
            $client = Client::firstOrCreate(
                ['email' => $validated['email']], // Procura por um cliente existente com o mesmo e-mail
                [
                    'name' => $validated['name'],
                    'company' => $validated['company'],
                    'phone' => $validated['phone'],
                ] // Se não encontrar, cria um novo com os dados fornecidos
            );

            // 2. Processar as respostas normais (sobrescrever se existir)
            foreach ($validated['answers'] as $answer) {
                ClientAnswer::updateOrCreate(
                    [
                        'client_id' => $client->id,
                        'question_id' => $answer['question_id'],
                    ], // Procura por uma resposta existente para este cliente e pergunta
                    [
                        'answer_id' => $answer['answer_id'],
                        'question_type' => 'normal' // Adicionado question_type
                    ] // Se encontrar, atualiza; se não, cria uma nova
                );
            }

            // 3. Processar as respostas de múltipla escolha (sobrescrever se existir)
            if (isset($validated['multiple_choice_answers'])) {
                foreach ($validated['multiple_choice_answers'] as $answer) {
                    ClientAnswer::updateOrCreate( //Usar o mesmo model ClientAnswer
                        [
                            'client_id' => $client->id,
                            'question_id' => $answer['question_id'],

                        ],
                        [
                            'answer_id' => $answer['answer_id'],
                            'question_type' => 'multiple_choice' // Adicionado question_type
                        ]
                    );
                }
            }

            DB::commit();

            //return redirect()->back()->with('success', 'Informações salvas com sucesso!'); //Não usar redirect com AJAX
            return response()->json(['success' => true, 'message' => 'Informações salvas com sucesso!']);


        } catch (\Exception $e) {
            DB::rollBack();
            //Não usar redirect com AJAX
            return response()->json(['success' => false, 'message' => 'Erro ao salvar informações: ' . $e->getMessage()], 500);
        }
    }
}
