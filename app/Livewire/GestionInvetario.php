<?php

namespace App\Livewire;

    use App\Models\Producto;
    use App\Models\Categoria;
    use App\Models\Iva;

use Livewire\Component;

class GestionInvetario extends Component
{
    public $productos = [];
    public $categorias = [];
    public $iva = [];

    public function mount()
    {
        $this->productos = Producto::select('id', 'nombre', 'precio', 'imagen_url', 'iva_id', 'categoria_id', 'stock', 'se_vende')->with('categoria')->get()->keyBy('id')->toArray();
        $this->categorias = Categoria::select('id', 'nombre', 'imagen_url')->get()->keyBy('id')->toArray();
        $this->iva = Iva::select('id', 'qty')->get()->keyBy('id')->toArray();


    }
    public function crearProducto($nombre, $precio, $imagen_url, $iva_id, $categoria_id, $stock, $se_vende)
    {
        Producto::create([
            'id' => $id,
            'nombre' => $nombre,
            'precio' => $precio,
            'iva_id' => $iva_id,
            'categoria_id' => $categoria_id,
            'stock' => $stock,
            'se_vende' => $se_vende,
            'imagen_url' => $imagen_url
        ]);
        $this->productos = Producto::select('id', 'nombre', 'precio', 'imagen_url', 'iva_id', 'categoria_id', 'stock', 'se_vende')->with('categoria')->get()->keyBy('id')->toArray();
    }
    public function render()
    {
        return view('livewire.gestion-invetario');
    }
}
