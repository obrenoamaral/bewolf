<?php

namespace App\Http\Controllers; // Removido o \Api

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with('answers')->get();
        return view('questions.index', compact('questions'));
    }

    public function storeWithAnswer(Request $request)
    {
        try {
            // Inicia a transação
            DB::beginTransaction();

            // Valida os dados da requisição
            $validated = $request->validate([
                'question' => 'required|string', // Garante que a pergunta seja uma string
                'answers' => 'required|array|min:1', // Garante que haja pelo menos uma resposta
                'answers.*.answer' => 'required|string', // Garante que cada resposta seja uma string
                'answers.*.weight' => 'required|numeric', // Garante que o peso seja um número
                'answers.*.diagnosis' => 'required|string', // Garante que o diagnóstico seja uma string
                'answers.*.solution' => 'required|string', // Garante que a solução seja uma string
            ]);

            // Cria a pergunta
            $question = Question::create([
                'question' => $validated['question'],
            ]);

            // Cria as respostas associadas à pergunta
            foreach ($validated['answers'] as $answerData) {
                Answer::create([
                    'question_id' => $question->id,
                    'answer' => $answerData['answer'],
                    'weight' => $answerData['weight'],
                    'diagnosis' => $answerData['diagnosis'],
                    'solution' => $answerData['solution'],
                ]);
            }

            // Confirma a transação
            DB::commit();

            // Redireciona com uma mensagem de sucesso
            return redirect('/questions')->with('success', 'Pergunta e respostas cadastradas com sucesso!');
        } catch (\Exception $e) {
            // Desfaz a transação em caso de erro
            DB::rollBack();

            // Redireciona com uma mensagem de erro
            return redirect('/questions')->with('error', 'Erro ao cadastrar pergunta e respostas: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $question = Question::find($id);

        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }

        $question->delete();

        return redirect()->route('questions.index')->with('success', 'Question deleted successfully.');
    }
}
