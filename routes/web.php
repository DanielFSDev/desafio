<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DocumentController;

Route::get('/register', [UserController::class, 'registerForm'])->name('register.view');
Route::post('/register', [UserController::class, 'register'])->name('register');
Route::get('/login', [UserController::class, 'loginForm'])->name('login.view');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::middleware(['auth'])->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index.view');
    Route::get('/documents',[DocumentController::class, 'documentsView'])->name('documents');
    Route::post('/upload', [DocumentController::class, 'upload'])->name('document.upload');
    Route::get('/documents/edition', [DocumentController::class, 'variablesView'])->name('documents.variables.view');
    Route::post('/documents/edition', [DocumentController::class, 'saveVariables'])->name('documents.variables');
    Route::get('/documents/pdf', [DocumentController::class, 'downloadPdf'])->name('documents.download.pdf');
    Route::get('/documents/docx', [DocumentController::class, 'downloadDocx'])->name('documents.download.docx');
    Route::delete('/documents', [DocumentController::class, 'delete'])->name('documents.delete');
});
