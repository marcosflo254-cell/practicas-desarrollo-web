<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller {
    public function index() {
        return response()->json(Producto::with('categoria')->get());
    }

    public function store(Request $request) {
        $campos = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric',
            'categoria_id' => 'required|exists:categorias,id'
        ]);

        $producto = Producto::create($campos);
        return response()->json(['message' => 'Creado con éxito', 'producto' => $producto], 201);
    }

    public function update(Request $request, $id) {
        $producto = Producto::findOrFail($id);
        
        $campos = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric',
            'categoria_id' => 'required|exists:categorias,id'
        ]);

        $producto->update($campos);
        return response()->json(['message' => 'Actualizado con éxito', 'producto' => $producto]);
    }

    public function destroy($id) {
        $producto = Producto::findOrFail($id);
        $producto->delete();
        return response()->json(['message' => 'Eliminado con éxito']);
    }
}