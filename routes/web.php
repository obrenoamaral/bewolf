<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FormController::class, 'showForm'])->name('form.show');
Route::post('/form/submit', [FormController::class, 'submitForm'])->name('form.submit');
Route::post('/form/submit-info', [FormController::class, 'submitInfo'])->name('form.submitInfo');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

    Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
    Route::get('/questions/create', function () {return view('questions.create');})->name('questions.create');
    Route::post('/questions', [QuestionController::class, 'storeWithAnswer'])->name('questions.store');
    Route::delete('/questions/{id}', [QuestionController::class, 'destroy'])->name('questions.destroy');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients/store', [ClientController::class, 'store'])->name('clients.store');
});

Route::post('/diagnosis/generate', [DiagnosisController::class, 'generateDiagnosis'])->name('diagnosis.generate');
Route::get('/diagnosis/{client_id}', [DiagnosisController::class, 'generateReport']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
