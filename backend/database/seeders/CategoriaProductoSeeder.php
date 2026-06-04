<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Support\Str;

class CategoriaProductoSeeder extends Seeder {
    public function run(): void {
        $categorias = ['Electrónica', 'Ropa', 'Hogar', 'Deportes'];

        foreach ($categorias as $nombre) {
            $cat = Categoria::create([
                'nombre' => $nombre,
                'slug'   => Str::slug($nombre),
                'descripcion' => "Productos del área de $nombre"
            ]);

            // Genera los 5 productos amarrados a cada categoría
            for ($i = 1; $i <= 5; $i++) {
                Producto::create([
                    'nombre' => "$nombre - Producto $i",
                    'descripcion' => "Descripción de prueba para el producto $i.",
                    'precio' => rand(150, 4000),
                    'categoria_id' => $cat->id
                ]);
            }
        }
    }
}