<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientAnswer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $clients = Client::count();
        $forms = Client::count();
        $questions = Question::count();

        $formCounts = ClientAnswer::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $labels = $formCounts->pluck('date');
        $data = $formCounts->pluck('total');

        return view('dashboard', compact('clients', 'forms', 'questions', 'labels', 'data'));
    }

}
