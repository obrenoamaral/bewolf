<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\ClientAnswer;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;



class DiagnosisController extends Controller
{
    public function generateDiagnosis(Request $request)
    {
        // Validar as respostas
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|exists:answers,id',
        ]);

        // Buscar as respostas selecionadas
        $answers = Answer::whereIn('id', $validated['answers'])->get();

        // Extrair diagnósticos e soluções
        $diagnoses = $answers->pluck('diagnosis')->unique();
        $solutions = $answers->pluck('solution')->unique();

        // Calcular a pontuação total (se necessário)
        $totalWeight = $answers->sum('weight');

        // Retornar a view com o relatório
        return view('reports.index', [
            'diagnoses' => $diagnoses,
            'solutions' => $solutions,
            'totalWeight' => $totalWeight, // Opcional: passar a pontuação total
        ]);
    }

    private function calculateDiagnosis($totalWeight)
    {
        if ($totalWeight <= 10) {
            return 'Baixo risco';
        } elseif ($totalWeight <= 20) {
            return 'Risco moderado';
        } else {
            return 'Alto risco';
        }
    }

    public function generateReport($client_id)
    {
        $clientAnswers = ClientAnswer::where('client_id', $client_id)
            ->with('answer', 'question')
            ->get();

        if ($clientAnswers->isEmpty()) {
            return response()->json(['message' => 'No reports found for this client.'], 404);
        }

        $reportData = $clientAnswers->map(function ($clientAnswer) {
            return [
                'question' => $clientAnswer->question->question,
                'diagnosis' => $clientAnswer->answer->diagnosis,
                'solution' => $clientAnswer->answer->solution,
            ];
        });

        // Gerando o PDF corretamente
        $pdf = Pdf::loadView('reports.index', compact('reportData'));

//        return $pdf->download('reports.pdf');
        return view('reports.index', compact('reportData'));
    }


}
