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
}
