<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller {
    // Retorna las categorías incluyendo sus productos anidados (Eager Loading)
    public function index() {
        return response()->json(Categoria::with('productos')->get());
    }

    public function productos(Categoria $categoria) {
        return response()->json([
            'categoria' => $categoria->nombre,
            'productos' => $categoria->productos
        ]);
    }
}