<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {return view('welcome');});
Route::get('/register', [UserController::class, 'showRegisterForm'])->name('register.view');
Route::post('/register', [UserController::class, 'register'])->name('register');
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login.view');
Route::post('/login', [UserController::class, 'login'])->name('login');
