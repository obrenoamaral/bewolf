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
            // Validação dos dados
            $validated = $request->validate([
                'question_title' => 'required|string|max:255',
                'answers' => 'required|array|min:1',
                'answers.*.answer' => 'required|string|max:255',
                'answers.*.weight' => 'required|integer',
                'answers.*.diagnosis' => 'required|string',
            ]);

            // Cria a pergunta
            $question = QuestionMultipleChoice::create([
                'question_title' => $validated['question_title'],
            ]);

            // Cria as respostas associadas à pergunta
            foreach ($validated['answers'] as $answer) {
                AnswersMultipleChoice::create([
                    'question_multiple_choice_id' => $question->id,
                    'answer' => $answer['answer'],
                    'weight' => $answer['weight'],
                    'diagnosis' => $answer['diagnosis'],
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
            ]);

            $question = QuestionMultipleChoice::findOrFail($id);
            $question->update($validated);

            return redirect()->back()->with('success', 'Pergunta atualizada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao atualizar a pergunta: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('multipleChoices.create'); // Retorna a view de criação
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
