<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientAnswer;
use App\Models\QuestionMultipleChoice; // Importe o model de múltipla escolha
use App\Models\AnswersMultipleChoice; // Importe o model de respostas de múltipla escolha
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule; // Importante para validação unique
use Illuminate\Support\Str;

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

                // 1. Encontrar ou criar o cliente (mantido como antes)
                $client = Client::firstOrCreate(
                    ['email' => $validated['email']],
                    [
                        'name' => $validated['name'],
                        'company' => $validated['company'],
                        'phone' => $validated['phone'],
                    ]
                );

                // 2. Gerar o submission_id
                $submissionId = Str::uuid()->toString();
                Log::warning($submissionId);

                // 3. Processar as respostas normais (adicionar submission_id)
                foreach ($validated['answers'] as $answer) {
                    ClientAnswer::updateOrCreate(
                        [
                            'client_id' => $client->id,
                            'question_id' => $answer['question_id'],
                            'submission_id' => $submissionId, // Adicionado o submission_id aqui
                        ],
                        [
                            'answer_id' => $answer['answer_id'],
                            'question_type' => 'normal',
                            'submission_id' => $submissionId, // E aqui também
                        ]
                    );
                }
                Log::warning($validated['multiple_choice_answers']);

                // 4. Processar as respostas de múltipla escolha (adicionar submission_id)
                if (isset($validated['multiple_choice_answers'])) {
                    foreach ($validated['multiple_choice_answers'] as $answer) {
                        ClientAnswer::updateOrCreate(
                            [
                                'client_id' => $client->id,
                                'question_id' => $answer['question_id'],
                                'submission_id' => $submissionId, // Adicionado o submission_id aqui
                            ],
                            [
                                'answer_id' => $answer['answer_id'],
                                'question_type' => 'multiple_choice',
                                'submission_id' => $submissionId, // E aqui também
                            ]
                        );
                    }
                }

                DB::commit();

                return response()->json(['success' => true, 'message' => 'Informações salvas com sucesso!']);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Erro ao salvar informações: ' . $e->getMessage()], 500);
            }
        }
}
