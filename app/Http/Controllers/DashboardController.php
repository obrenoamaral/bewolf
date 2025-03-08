<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientAnswer;
use App\Models\Question;
use App\Models\QuestionMultipleChoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::count();
//        $forms = ClientAnswer::count(); // Corrigido para contar respostas de formulários
        $questions = Question::count();
        $multipleChoices = QuestionMultipleChoice::count();

        // Datas de início e fim (com valores padrão):
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30);
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();


        // Consulta para obter os dados do gráfico, filtrando por data:
        $formCounts = ClientAnswer::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $labels = $formCounts->pluck('date');
        $data = $formCounts->pluck('total');

        return view('dashboard', compact('clients', 'multipleChoices', 'questions', 'labels', 'data', 'startDate', 'endDate'));
    }
}
