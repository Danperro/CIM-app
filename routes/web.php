<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Control\Control;
use App\Livewire\Equipos\AllEquipos;
use App\Livewire\Reportes\AllReportes;
use App\Livewire\Usuarios\AllUsuarios;
use Livewire\Volt\Volt;
Route::redirect('/', '/Control');
//Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
//Route::post('/login', [AuthController::class, 'login']);
Route::get('/Control', Control::class);
Route::get('/Equipos', AllEquipos::class);
Route::get('/Usuarios', AllUsuarios::class);
Route::get('/Reportes', AllReportes::class);
Route::get('/reporte/pdf/{idDtl}', [AllReportes::class, 'generarPDF'])->name('reporte.pdf');
