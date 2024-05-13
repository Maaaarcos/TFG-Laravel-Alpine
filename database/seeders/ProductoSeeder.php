<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define sample data
        $productos = [
            [
                'nombre' => 'Producto 1',
                'precio' => 10.50,
                'imagen_url' => 'https://via.placeholder.com/150',
                'categoria_id' => 1,
                'iva_id' => 1,
            ],
            [
                'nombre' => 'Producto 2',
                'precio' => 20.75,
                'imagen_url' => 'https://via.placeholder.com/150',
                'categoria_id' => 1,
                'iva_id' => 1,
            ],
            
        ];

        // Insert data into the productos table
        foreach ($productos as $productoData) {
            Producto::create($productoData);
        }
    }
}
