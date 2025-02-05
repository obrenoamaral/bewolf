<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientAnswer;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Armazena as informações do cliente e suas respostas.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     */

    public function index()
    {
        $clients = Client::all();
//        return response()->json($clients);
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
            ]);

            $client = Client::create([
                'name' => $validated['name'],
                'company' => $validated['company'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);

            foreach ($validated['answers'] as $answer) {
                ClientAnswer::create([
                    'client_id' => $client->id,
                    'question_id' => $answer['question_id'],
                    'answer_id' => $answer['answer_id'],
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Informações salvas com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Erro ao salvar informações: ' . $e->getMessage());
        }
    }
}
