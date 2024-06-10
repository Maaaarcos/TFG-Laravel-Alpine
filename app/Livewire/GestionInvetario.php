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
    public $productoFiltrado = [];
    public $categoriaFiltrada = [];
    public $ivaFiltrado = [];

    public function mount()
    {
        $this->productos = Producto::select('id', 'nombre', 'precio', 'imagen_url', 'iva_id', 'categoria_id', 'stock', 'se_vende')->with('categoria')->get()->keyBy('id')->toArray();
        $this->categorias = Categoria::select('id', 'nombre', 'imagen_url')->get()->keyBy('id')->toArray();
        $this->iva = Iva::select('id', 'qty')->get()->keyBy('id')->toArray();


    }
    public function crearProducto($nombre, $precio, $iva_id, $categoria_id, $stock, $se_vende, $imagen_url= null)
    {
        Producto::create([
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
    public function crearCategoria($nombre, $imagen_url= null)
    {
        Categoria::create([
            'nombre' => $nombre,
            'imagen_url' => $imagen_url
        ]);
        $this->categorias = Categoria::select('id', 'nombre', 'imagen_url')->get()->keyBy('id')->toArray();
    }
    public function crearIva($qty)
    {
        Iva::create([
            'qty' => $qty
        ]);
        $this->iva = Iva::select('id', 'qty')->get()->keyBy('id')->toArray();
    }
    public function getProducto($id)
    {
        $this->productoFiltrado = Producto::select('id', 'nombre', 'precio', 'imagen_url', 'iva_id', 'categoria_id', 'stock', 'se_vende')->where('id', $id)->with('categoria')->get()->keyBy('id')->toArray();
        //dd($this->productoFiltrado);
    }
    public function getCategoria($id)
    {
        $this->categoriaFiltrada = Categoria::select('id', 'nombre', 'imagen_url')->where('id', $id)->get()->keyBy('id')->toArray();
        //dd($this->productoFiltrado);
    }
    public function getCaja($id)
    {
        $this->productoFiltrado = Caja::select('id', 'nombre', 'precio', 'imagen_url', 'iva_id', 'categoria_id', 'stock', 'se_vende')->where('id', $id)->get()->keyBy('id')->toArray();
        //dd($this->productoFiltrado);
    }
    public function getIvas($id)
    {
        $this->ivaFiltrado = Iva::select('id', 'qty')->where('id', $id)->get()->keyBy('id')->toArray();
        //dd($this->productoFiltrado);
    }
    public function actualizarProducto($id, $nombre, $precio, $iva_id, $categoria_id, $stock, $se_vende, $imagen_url = null)
    {
            $producto = Producto::findOrFail($id);
            $producto->update([
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
    public function dropProducto($id)
    {
            $producto = Producto::findOrFail($id);
            $producto->delete();

            $this->productos = Producto::select('id', 'nombre', 'precio', 'imagen_url', 'iva_id', 'categoria_id', 'stock', 'se_vende')->with('categoria')->get()->keyBy('id')->toArray();
    }
    public function dropCategoria($id)
    {
            $categoria = Categoria::findOrFail($id);
            $categoria->delete();

            $this->categorias = Categoria::select('id', 'nombre', 'imagen_url')->get()->keyBy('id')->toArray();
    }
    public function dropIva($id)
    {
            $iva = Iva::findOrFail($id);
            $iva->delete();

            $this->iva = Iva::select('id', 'qty')->get()->keyBy('id')->toArray();
    }
    public function render()
    {
        return view('livewire.gestion-invetario');
    }
}
