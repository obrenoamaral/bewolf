<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FormController::class, 'showForm'])->name('form.show');
Route::post('/form/submit-info', [FormController::class, 'submitInfo'])->name('form.submitInfo');

Route::get('/thankyou', function () {return view('thanks');})->name('thanks');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});



Route::middleware(['auth'])->group(function () {

    Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
    Route::get('/questions/create', function () {return view('questions.create');})->name('questions.create');
    Route::post('/questions', [QuestionController::class, 'storeWithAnswer'])->name('questions.store');
    Route::delete('/questions/{id}', [QuestionController::class, 'destroy'])->name('questions.destroy');
    Route::put('/questions/{id}', [QuestionController::class, 'update'])->name('questions.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients/store', [ClientController::class, 'store'])->name('clients.store');
});

Route::get('/diagnosis/{client_id}/report', [DiagnosisController::class, 'generateReport']);
Route::get('/diagnosis/{client_id}/calculate', [DiagnosisController::class, 'calculateDiagnosis']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/send-report/{client_id}', [DiagnosisController::class, 'sendReportByEmail']);


require __DIR__.'/auth.php';
