<?php

use App\Http\Controllers\addJugadores;
use App\Http\Controllers\Asistencia;
use App\Http\Controllers\Equipos;
use App\Http\Controllers\Faltas;
use App\Http\Controllers\Jugadores;
use App\Http\Controllers\Ejercicios;
use App\Http\Controllers\Partido;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();
Route::group(["middleware" => 'rolMiddleware'], function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::post('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/equipos', [Equipos::class, 'index'])->name('equipos');
    Route::post('/equipos', [Equipos::class, 'index'])->name('equipos');

    Route::get('/asistencia', [Asistencia::class, 'index'])->name('asistencia');
    Route::post('/asistencia', [Asistencia::class, 'index'])->name('asistencia');

    Route::get('/partido', [Partido::class, 'index'])->name('partido');
    Route::post('/partido', [Partido::class, 'index'])->name('partido');

    Route::get('/jugadores', [Jugadores::class, 'index'])->name('jugadores');
    Route::post('/jugadores', [Jugadores::class, 'index'])->name('jugadores');

    Route::get('/faltas', [Faltas::class, 'index'])->name('faltas');
    Route::post('/faltas', [Faltas::class, 'index'])->name('faltas');

    Route::get('/ejercicios', [Ejercicios::class, 'index'])->name('ejercicios');
    Route::post('/ejercicios', [Ejercicios::class, 'index'])->name('ejercicios');

    Route::get('/new-jugadores', [addJugadores::class, 'index'])->name('new-jugadores');
    Route::post('/new-jugadores', [addJugadores::class, 'index'])->name('new-jugadores');

});
