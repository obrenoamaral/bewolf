<?php

use App\Exports\ClientsExport;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\EmailContentController;
use App\Http\Controllers\QuestionMultipleChoiceController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', [FormController::class, 'showForm'])->name('form.show');
Route::post('/form/submit-info', [FormController::class, 'submitInfo'])->name('form.submitInfo');
Route::get('/thankyou', function () { return view('thanks'); })->name('thanks');

// Rotas protegidas por autenticação
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perguntas
    Route::prefix('questions')->group(function () {
        Route::get('/', [QuestionController::class, 'index'])->name('questions.index');
        Route::get('/create', function () { return view('questions.create'); })->name('questions.create');
        Route::post('/', [QuestionController::class, 'storeWithAnswer'])->name('questions.store');
        Route::delete('/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy'); // {question}
        Route::put('/{question}', [QuestionController::class, 'update'])->name('questions.update');   // {question}
        Route::get('/{question}', [QuestionController::class, 'edit'])->name('questions.edit'); // ADICIONE ESTA LINHA
    });

    // Clientes
    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('clients.index');
        Route::post('/store', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/export', function () {
            return Excel::download(new ClientsExport, 'clients.xlsx');
        })->name('clients.export');
    });

    // Diagnóstico
    Route::prefix('diagnosis')->group(function () {
        Route::get('/{client_id}/report/{submission_id}', [DiagnosisController::class, 'generateReport']); // COM submission_id
        Route::get('/{client_id}/calculate', [DiagnosisController::class, 'calculateDiagnosis']); // Não usada
    });

    // Perfil do usuário
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Enviar relatório por e-mail
    Route::post('/send-report/{client_id}/{submission_id}', [DiagnosisController::class, 'sendReportByEmail']); // COM submission_id
    Route::get('/report/preview/{client_id}/{submission_id}', [DiagnosisController::class, 'previewReport']); // COM submission_id

    // Email (conteúdo de e-mail)
    Route::prefix('email')->group(function () {
        Route::get('/', [EmailContentController::class, 'index'])->name('email.index');
        Route::get('/edit', [EmailContentController::class, 'edit'])->name('email.edit');
        Route::post('/update', [EmailContentController::class, 'update'])->name('email.update');
    });

// Multiple Choices
    Route::prefix('multiple-choices')->group(function () {
        Route::get('/', [QuestionMultipleChoiceController::class, 'index'])->name('multiple-choices.index');
        Route::get('/create', [QuestionMultipleChoiceController::class, 'create'])->name('multiple-choices.create'); // Rota de criação
        Route::post('/', [QuestionMultipleChoiceController::class, 'store'])->name('multiple-choices.store'); // Rota para salvar a pergunta
        Route::put('/{id}', [QuestionMultipleChoiceController::class, 'update'])->name('multiple-choices.update'); // Rota para atualizar a pergunta
        Route::delete('/{id}', [QuestionMultipleChoiceController::class, 'destroy'])->name('multiple-choices.destroy'); // Rota para excluir a pergunta
    });

});

require __DIR__.'/auth.php';
