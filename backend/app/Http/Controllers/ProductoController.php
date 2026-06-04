<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf; // Líbreria para PDFs en Laravel

class ProductoController extends Controller {
    
    public function index() {
        return response()->json(Producto::with('categoria')->get());
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|min:3|max:50',
            'descripcion' => 'required|string|min:10|max:255',
            'precio' => 'required|numeric|min:1',
            'categoria_id' => 'required|exists:categorias,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $producto = Producto::create($request->all());
        return response()->json(['message' => '¡Producto registrado!', 'producto' => $producto], 201);
    }

    public function update(Request $request, $id) {
        $producto = Producto::findOrFail($id);
        $producto->update($request->all());
        return response()->json(['message' => '¡Producto actualizado!', 'producto' => $producto]);
    }

    public function destroy($id) {
        $producto = Producto::findOrFail($id);
        $producto->delete();
        return response()->json(['message' => 'Producto eliminado.']);
    }

    // FUNCIÓN NUEVA: Generar Reporte PDF formal
    public function exportarPDF() {
        $productos = Producto::with('categoria')->get();
        
        // Estructura HTML básica del PDF que se va a descargar
        $html = '
        <div style="font-family: Arial, sans-serif; padding: 20px;">
            <h1 style="text-align: center; color: #2c3e50; border-bottom: 2px solid #2c3e50; padding-bottom: 10px;">
                REPORTE OFICIAL DE INVENTARIO
            </h1>
            <p style="text-align: right; font-size: 12px; color: #7f8c8d;">Fecha de generación: ' . date('d/m/Y H:i') . '</p>
            
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background-color: #2c3e50; color: white; text-align: left;">
                        <th style="padding: 10px; border: 1px solid #ddd;">ID</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Producto</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Descripción</th>
                        <th style="padding: 10px; border: 1px solid #ddd;">Precio</th>
                    </tr>
                </thead>
                <tbody>';
                
        foreach($productos as $p) {
            $html .= '
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">' . $p->id . '</td>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">' . $p->nombre . '</td>
                        <td style="padding: 10px; border: 1px solid #ddd; color: #555;">' . $p->descripcion . '</td>
                        <td style="padding: 10px; border: 1px solid #ddd; color: #27ae60; font-weight: bold;">$' . $p->precio . '</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
            <div style="margin-top: 50px; text-align: center; font-size: 11px; color: #95a5a6;">
                Sistemas Computacionales - Universidad Politécnica de Texcoco
            </div>
        </div>';

        $pdf = Pdf::loadHTML($html);
        return $pdf->download('reporte-inventario.pdf');
    }
}