<?php

namespace App\Http\Controllers;

use App\Models\QuestionMultipleChoice;
use App\Models\AnswersMultipleChoice;
use Illuminate\Http\Request;

class QuestionMultipleChoiceController extends Controller
{
    public function index()
    {
        $questionsMultipleChoice = QuestionMultipleChoice::with('answersMultipleChoice')->get();
        return view('multipleChoices.index', compact('questionsMultipleChoice'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'question_title' => 'required|string|max:255',
                'solution_title' => 'required|string|max:255',
                'answers' => 'required|array|min:1',
                'answers.*.answer' => 'required|string|max:255',
                'answers.*.weight' => 'required|integer',
                'answers.*.diagnosis' => 'required|string',
                'answers.*.strength_weakness_title' => 'nullable|string',
                'answers.*.strength_weakness' => 'nullable|in:strong,weak',
            ]);

            $question = QuestionMultipleChoice::create([
                'question_title' => $validated['question_title'],
                'solution_title' => $validated['solution_title'],
            ]);

            foreach ($validated['answers'] as $answer) {
                AnswersMultipleChoice::create([
                    'question_multiple_choice_id' => $question->id,
                    'answer' => $answer['answer'],
                    'weight' => $answer['weight'],
                    'diagnosis' => $answer['diagnosis'],
                    'strength_weakness_title' => $answer['strength_weakness_title'] ?? null,
                    'strength_weakness' => $answer['strength_weakness'] ?? null,
                ]);
            }

            return redirect()->route('multiple-choices.index')->with('successMessage', 'Pergunta e respostas criadas com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('errorMessage', 'Erro ao criar a pergunta: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'question_title' => 'required|string|max:255',
                'solution_title' => 'required|string|max:255',
                'answers' => 'required|array',
                'answers.*.answer' => 'required|string',
                'answers.*.weight' => 'required|integer',
                'answers.*.diagnosis' => 'nullable|string',
                'answers.*.strength_weakness_title' => 'nullable|string',
                'answers.*.strength_weakness' => 'nullable|in:strong,weak',
            ]);

            $question = QuestionMultipleChoice::findOrFail($id);

            $question->update([
                'question_title' => $validated['question_title'],
                'solution_title' => $validated['solution_title'],
            ]);

            $question->answersMultipleChoice()->delete();

            foreach ($validated['answers'] as $answerData) {
                $question->answersMultipleChoice()->create([
                    'answer' => $answerData['answer'],
                    'weight' => $answerData['weight'],
                    'diagnosis' => $answerData['diagnosis'],
                    'strength_weakness_title' => $answerData['strength_weakness_title'] ?? null,
                    'strength_weakness' => $answerData['strength_weakness'] ?? null,
                ]);
            }

            return response()->json(['message' => 'Pergunta atualizada com sucesso!'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar a pergunta: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        return view('multipleChoices.create');
    }

    public function destroy($id)
    {
        try {
            $question = QuestionMultipleChoice::findOrFail($id);
            $question->delete();

            return redirect()->back()->with('success', 'Pergunta removida com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao remover a pergunta: ' . $e->getMessage());
        }
    }
}
