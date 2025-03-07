<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException; // Import correto

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with('answers')->get();
        return view('questions.index', compact('questions'));
    }

    // Método edit (FALTAVA - ESSENCIAL)
    public function edit(Question $question)
    {
        $question->load('answers'); // Carrega as respostas
        return response()->json($question); // Retorna como JSON
    }

    public function storeWithAnswer(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'question' => 'required|string',
                'diagnosis_title' => 'required|string',
                'answers' => 'required|array|min:1',
                'answers.*.answer' => 'required|string',
                'answers.*.weight' => 'required|numeric',
                'answers.*.diagnosis' => 'required|string',
                'answers.*.solution' => 'required|string',
                'answers.*.strength_weakness_title' => 'nullable|string',
                'answers.*.strength_weakness' => 'nullable|in:strong,weak',
            ]);

            $question = Question::create([
                'question' => $validated['question'],
                'diagnosis_title' => $validated['diagnosis_title'],
            ]);

            foreach ($validated['answers'] as $answerData) {
                Answer::create([
                    'question_id' => $question->id,
                    'answer' => $answerData['answer'],
                    'weight' => $answerData['weight'],
                    'diagnosis' => $answerData['diagnosis'],
                    'solution' => $answerData['solution'],
                    'strength_weakness_title' => $answerData['strength_weakness_title'] ?? null,
                    'strength_weakness' => $answerData['strength_weakness'] ?? null,
                ]);
            }

            DB::commit();

            return redirect('/questions')->with('success', 'Pergunta e respostas cadastradas com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect('/questions')->with('error', 'Erro ao cadastrar pergunta e respostas: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Question $question)
    {
        $validatedData = $request->validate([
            'question' => 'required|string|max:255',
            'answers' => 'required|array',
            'answers.*.answer' => 'required|string|max:255',
            'answers.*.weight' => 'required|integer',
            'answers.*.diagnosis' => 'nullable|string',
            'answers.*.solution' => 'nullable|string',
            'answers.*.strength_weakness_title' => 'nullable|string|max:255',
            'answers.*.strength_weakness' => 'nullable|in:strong,weak',
            'answers.*.id' => 'nullable|integer',
        ]);

        DB::beginTransaction();

        try {
            // Atualiza a pergunta
            $question->update(['question' => $validatedData['question']]);

            // Atualiza/Cria/Deleta as respostas
            $existingAnswerIds = [];

            foreach ($validatedData['answers'] as $answerData) {
                if (isset($answerData['id'])) {
                    // Atualiza resposta existente
                    $answer = Answer::findOrFail($answerData['id']);
                    $answer->update([
                        'answer' => $answerData['answer'],
                        'weight' => $answerData['weight'],
                        'diagnosis' => $answerData['diagnosis'],
                        'solution' => $answerData['solution'],
                        'strength_weakness_title' => $answerData['strength_weakness_title'],
                        'strength_weakness' => $answerData['strength_weakness'],
                    ]);
                    $existingAnswerIds[] = $answer->id;
                } else {
                    // Cria nova resposta
                    $answer = new Answer([
                        'answer' => $answerData['answer'],
                        'weight' => $answerData['weight'],
                        'diagnosis' => $answerData['diagnosis'],
                        'solution' => $answerData['solution'],
                        'strength_weakness_title' => $answerData['strength_weakness_title'],
                        'strength_weakness' => $answerData['strength_weakness'],
                    ]);
                    $question->answers()->save($answer);
                    $existingAnswerIds[] = $answer->id;
                }
            }

            $question->answers()->whereNotIn('id', $existingAnswerIds)->delete();
            DB::commit();

            // RETORNO JSON EM CASO DE SUCESSO (Código 200 OK)
            return response()->json(['message' => 'Pergunta e respostas atualizadas com sucesso!'], 200);

        } catch (ValidationException $e) {
            DB::rollBack();
            // RETORNO JSON EM CASO DE ERRO DE VALIDAÇÃO (Código 422 Unprocessable Entity)
            return response()->json(['errors' => $e->errors()], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            // RETORNO JSON EM CASO DE OUTROS ERROS (Código 500 Internal Server Error)
            return response()->json(['error' => 'Erro ao atualizar pergunta e respostas: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id) //Você pode usar o Route Model Binding aqui também.
    {
        $question = Question::find($id); //Ou findOrFail

        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }

        $question->delete();

        return redirect()->route('questions.index')->with('success', 'Question deleted successfully.');
    }
}
