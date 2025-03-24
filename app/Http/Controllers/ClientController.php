<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientAnswer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{

    protected $diagnosisController;

    public function __construct(DiagnosisController $diagnosisController)
    {
        $this->diagnosisController = $diagnosisController;
    }

    public function index()
    {
        $clients = Client::all();
        return view('clients.index', compact('clients'));
    }

    public function resendEmail(Request $request, $id)
    {
        try {
            $request->validate([
                'id' => 'required|integer|exists:client_answers,id',
            ]);

            $client = ClientAnswer::where('client_id', $id)->first();

            if (!$client) {
                return response()->json(['message' => 'Cliente não encontrado.'], 404);
            }

            $clientId = $client->client_id;
            $submissionId = $client->submission_id;
            $fileName = 'diagnostico_' . $clientId . '_' . $submissionId . '.pdf';
            $fullPath = Storage::disk('public')->path($fileName);

            if (!Storage::disk('public')->exists($fileName)) {
                if (!file_exists($fullPath)) {
                    return response()->json(['message' => 'Arquivo PDF não encontrado.'], 404);
                }
            }

            $this->diagnosisController->sendReportByEmail($clientId, $submissionId, $fullPath);
            return response()->json(['message' => 'E-mail reenviado com sucesso!'], 200);

        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao reenviar e-mail: ' . $e->getMessage()], 500);
        }
    }
}
