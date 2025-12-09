<?php

use App\Http\Controllers\ContratoController;
use Illuminate\Support\Facades\Route;

Route::get('/contratos', [ContratoController::class, 'indexWeb'])->name('contrato.web.index');
