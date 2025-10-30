<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Livewire\Control\Control;
use App\Livewire\Mantenimiento\AllMantenimiento;
use App\Livewire\Laboratorios\AllLaboratorios;
use App\Livewire\Areas\AllArea;
use App\Livewire\Ayuda\AllAyuda;
use App\Livewire\Equipos\AllEquipos;
use App\Livewire\Incidencia\AllIncidencia;
use App\Livewire\Perfil\AllPerfil;
use App\Livewire\Perifericos\AllPeriferico;
use App\Livewire\Usuarios\AllUsuarios;
use App\Livewire\Reportes\AllReportes;
use App\Livewire\ReportesI\AllReportesI;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PerifericoEtiquetaController;
use App\Livewire\ConectividadIncidencia\ConectividadIncidencia;
use App\Livewire\ReporteConectividadIncidencia\ReporteConectividadIncidencia;

// Si alguien va a / y no está logueado: login. Si está logueado, ya caerá en /Control por intended.
Route::get('/', function () {
    return Auth::check()
        ? redirect('/Control')
        : redirect()->route('login');
});
// Invitados: solo login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Autenticados: todo lo demás + logout
Route::middleware('auth')->group(function () {
    Route::get('/Control', Control::class);
    Route::get('/Mantenimientos', AllMantenimiento::class);
    Route::get('/Laboratorios', AllLaboratorios::class);
    Route::get('/Areas', AllArea::class);
    Route::get('/Equipos', AllEquipos::class);
    Route::get('/Perifericos', AllPeriferico::class);
    Route::get('/Usuarios', AllUsuarios::class);
    Route::get('/Ayuda', AllAyuda::class);
    Route::get('/Perfil', AllPerfil::class);
    Route::get('/Reportes', AllReportes::class);
    Route::get('/ReportesConectividadIncidencia', ReporteConectividadIncidencia::class);
    Route::get('/Incidencias', AllIncidencia::class);
    Route::get('/ConectividadIncidencia', ConectividadIncidencia::class);
    Route::get('/ReportesIncidencias', AllReportesI::class);
    Route::get('/reporte/pdf/{idDtl}', [AllReportes::class, 'generarPDF'])->name('reporte.pdf');
    Route::get('/reporte/pdf/{idDtl}', [ReporteConectividadIncidencia::class, 'generarPDF'])->name('reporte.pdf');

    Route::get('/perifericos/{pef}/etiqueta', [PerifericoEtiquetaController::class, 'single'])
        ->name('perifericos.etiqueta'); // 1 etiqueta

    Route::post('/perifericos/etiquetas/pdf', [PerifericoEtiquetaController::class, 'bulkPdf'])
        ->name('perifericos.etiquetas.pdf'); // varias etiquetas en PDF

    // Logout por POST
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/activar-usuario/{user}', [AuthController::class, 'activar'])
    ->name('users.activate')          // ← este nombre debe coincidir con el que usas en el URL::temporarySignedRoute
    ->middleware(['signed', 'throttle:6,1']); // firma obligatoria y rate limit