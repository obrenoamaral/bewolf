<?php

namespace App\Http\Controllers; // Removido o \Api

use App\Models\Answer;
use App\Models\Question;
use Cassandra\Exception\ValidationException;
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
            DB::beginTransaction();

            $validated = $request->validate([
                'question' => 'required|string',
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

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validate([
                'question' => 'required|string|max:255',
                'answers' => 'required|array|min:1',
                'answers.*.answer' => 'required|string|max:255',
                'answers.*.diagnosis' => 'nullable|string|max:255',
                'answers.*.solution' => 'nullable|string',
                'answers.*.weight' => 'required|integer|min:1',
                'answers.*.strength_weakness_title' => 'required|string',
            ]);

            $question = Question::findOrFail($id);

            $question->question = $validatedData['question'];
            $question->save();

            $question->answers()->delete();

            foreach ($validatedData['answers'] as $answerData) {
                $answer = new Answer($answerData);
                $answer->question_id = $question->id;
                $answer->save();
            }

            DB::commit();

            return redirect('/questions')->with('success', 'Pergunta e respostas atualizadas com sucesso!');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao atualizar pergunta e respostas. ' . $e->getMessage())->withInput();
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
