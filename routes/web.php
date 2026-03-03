<?php

use Illuminate\Support\Facades\Route;

// Si quieres que al entrar a la raíz "/" mande directamente al login
Route::get('/', function () {
    return redirect('/login');
});

// Cambiamos /auth por /login
Route::get('/login', function () {
    return view('auth'); // La vista se sigue llamando auth.blade.php
})->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');