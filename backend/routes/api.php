<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;

Route::get('/categorias', [CategoriaController::class, 'index']);
Route::get('/categorias/{categoria}/productos', [CategoriaController::class, 'productos']);