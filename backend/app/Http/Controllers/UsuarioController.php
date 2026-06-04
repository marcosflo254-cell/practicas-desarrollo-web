<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Usamos el modelo nativo de Laravel para usuarios

class UsuarioController extends Controller {
    // 1. Listar todos los usuarios con su rol simulado o asignado
    public function index() {
        return response()->json(User::all());
    }

    // 2. Registrar un nuevo usuario (Administrador / Empleado)
    public function store(Request $request) {
        $campos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'rol' => 'required|string' // Ej: 'Administrador' o 'Empleado'
        ]);

        // Guardamos el usuario cifrando la contraseña
        $usuario = User::create([
            'name' => $campos['name'],
            'email' => $campos['email'],
            'password' => bcrypt($campos['password']),
            'rol' => $campos['rol'] 
        ]);

        return response()->json(['message' => 'Usuario registrado con éxito', 'usuario' => $usuario], 201);
    }

    // 3. Eliminar un usuario
    public function destroy($id) {
        $usuario = User::findOrFail($id);
        $usuario->delete();
        return response()->json(['message' => 'Usuario eliminado con éxito']);
    }
}