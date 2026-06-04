<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller {
    
    public function index() {
        return response()->json(Producto::with('categoria')->get());
    }

    // Registrar con Validación Avanzada
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|min:3|max:50',
            'descripcion' => 'required|string|min:10|max:255',
            'precio' => 'required|numeric|min:1|max:999999',
            'categoria_id' => 'required|exists:categorias,id'
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'descripcion.required' => 'Debes añadir una descripción detallada.',
            'descripcion.min' => 'La descripción debe ser de al menos 10 caracteres.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número válido.',
            'precio.min' => 'El precio mínimo debe ser de $1.00.',
            'categoria_id.exists' => 'La categoría seleccionada no existe en la base de datos.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $producto = Producto::create($request->all());
        return response()->json(['message' => '¡Producto registrado con éxito!', 'producto' => $producto], 201);
    }

    // Actualizar con Validación Avanzada
    public function update(Request $request, $id) {
        $producto = Producto::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|min:3|max:50',
            'descripcion' => 'required|string|min:10|max:255',
            'precio' => 'required|numeric|min:1|max:999999',
            'categoria_id' => 'required|exists:categorias,id'
        ], [
            'nombre.required' => 'El nombre no puede quedar vacío.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria para actualizar.',
            'descripcion.min' => 'La descripción debe ser de al menos 10 caracteres.',
            'precio.required' => 'El precio es requerido.',
            'precio.numeric' => 'Ingresa un precio numérico válido.',
            'precio.min' => 'El precio debe ser mayor a $1.00.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $producto->update($request->all());
        return response()->json(['message' => '¡Producto actualizado correctamente!', 'producto' => $producto]);
    }

    public function destroy($id) {
        $producto = Producto::findOrFail($id);
        $producto->delete();
        return response()->json(['message' => 'Producto eliminado correctamente.']);
    }
}