<?php

use App\Http\Controllers\ContratoController;
use Illuminate\Support\Facades\Route;

Route::resource('contratos', ContratoController::class)->except(['create', 'edit']);
