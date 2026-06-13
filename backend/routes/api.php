<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Models\Pedido;
use App\Jobs\EnviarConfirmacionPedido; // Importante importar el Job que creamos antes
use Illuminate\Http\Request;

Route::post('/registrar', [AuthController::class, 'registrar']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/categorias', [CategoriaController::class, 'index']);
    Route::apiResource('productos', ProductoController::class);

    // 🚀 ENDPOINTS DE LA PRÁCTICA 11 (Añade estos dos exactamente)
    
    // 1. Ruta para registrar el pedido y despachar la tarea de fondo
    Route::post('/pedidos', function (Request $request) {
        $pedido = Pedido::create([
            'cliente' => 'Marcos Flores Vera', // Tu firma de evaluación
            'total' => 6800.00,
            'estado' => 'procesando'
        ]);

        // Despachamos el Job a la cola con un retraso intencional para ver el Polling
        EnviarConfirmacionPedido::dispatch($pedido)->delay(now()->addSeconds(5));

        return response()->json(['pedido_id' => $pedido->id, 'status' => 'procesando'], 201);
    });

    // 2. Ruta de Polling que Vue interroga cada 3 segundos
    Route::get('/pedidos/{id}', function ($id) {
        $pedido = Pedido::findOrFail($id);
        return response()->json([
            'pedido_id' => $pedido->id,
            'email_enviado_at' => $pedido->email_enviado_at // Si es null, Vue sigue esperando
        ]);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});