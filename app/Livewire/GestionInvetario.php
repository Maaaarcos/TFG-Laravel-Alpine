<?php

namespace App\Livewire;

    use App\Models\Producto;
    use App\Models\Categoria;
    use App\Models\Iva;
    use App\Models\Caja;
    
    use Livewire\WithFileUploads;
    

use Livewire\Component;

class GestionInvetario extends Component
{
    use WithFileUploads;

    public $nombre;
    public $precio;
    public $imagen;
    public $iva_id;
    public $categoria_id;
    public $stock;
    public $estado;

    protected $rules = [
        'nombre' => 'required|string',
        'precio' => 'required|numeric',
        'imagen' => 'required|image|max:2048', // 1MB Max
        'iva_id' => 'required|integer',
        'categoria_id' => 'required|integer',
        'stock' => 'required|integer',
        'estado' => 'required|boolean',
    ];
    
    public $productos = [];
    public $categorias = [];
    public $iva = [];
    public $caja = [];

    public function mount()
    {
        $this->productos = Producto::select('id', 'nombre', 'precio', 'imagen_url', 'iva_id', 'categoria_id', 'stock', 'se_vende')->with('categoria')->get()->keyBy('id')->toArray();
        $this->categorias = Categoria::select('id', 'nombre', 'imagen_url')->get()->keyBy('id')->toArray();
        $this->iva = Iva::select('id', 'qty')->get()->keyBy('id')->toArray();
        $this->caja = Caja::select('id', 'name')->get()->keyBy('id')->toArray();
        


    }
    public function crearProducto()
    {
        $this->validate();

        // Guardar la imagen
        $imagenPath = $this->imagen->store('productos', 'public');

        // Crear el producto (ejemplo)
        Producto::create([
            'nombre' => $this->nombre,
            'precio' => $this->precio,
            'imagen_url' => $imagenPath,
            'iva_id' => $this->iva_id,
            'categoria_id' => $this->categoria_id,
            'stock' => $this->stock,
            'estado' => $this->estado,
        ]);
        
        session()->flash('message', 'Producto creado exitosamente.');
    }
    // public function crearProducto($nombre, $precio, $iva_id, $categoria_id, $stock, $se_vende, $imagen_url)
    // {
    //     $imagenUrl = $this->imagen->store('imagenes_productos', 'public');

    //     Producto::create([
    //         'nombre' => $nombre,
    //         'precio' => $precio,
    //         'iva_id' => $iva_id,
    //         'categoria_id' => $categoria_id,
    //         'stock' => $stock,
    //         'se_vende' => $se_vende,
    //         'imagen_url' => $imagenUrl
    //     ]);
    //     $this->productos = Producto::select('id', 'nombre', 'precio', 'imagen_url', 'iva_id', 'categoria_id', 'stock', 'se_vende')->with('categoria')->get()->keyBy('id')->toArray();
    // }
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
    public function crearCaja($name)
    {
        Caja::create([
            'name' => $name
        ]);
        $this->caja = Caja::select('id', 'name')->get()->keyBy('id')->toArray();
    }
    public function actualizarProducto($id, $nombre, $precio, $iva_id, $categoria_id, $stock, $se_vende, $imagen_url = null)
    {
            $producto = Producto::find($id);
            if ($producto) {
    
                $producto->nombre = $nombre;
                $producto->precio = $precio;
                $producto->iva_id = $iva_id;
                $producto->categoria_id = $categoria_id;
                $producto->stock = $stock;
                $producto->se_vende = $se_vende;
                $producto->imagen_url = $imagen_url;
                $producto->save();
            }

            $this->productos = Producto::select('id', 'nombre', 'precio', 'imagen_url', 'iva_id', 'categoria_id', 'stock', 'se_vende')->with('categoria')->get()->keyBy('id')->toArray();
    }
    public function actualizarCategoria($id, $nombre, $imagen_url = null)
    {
            $categoria = Categoria::find($id);
            if ($categoria) {
    
                $categoria->nombre = $nombre;
                $categoria->imagen_url = $imagen_url;
                $categoria->save();
            }

            $this->categorias = Categoria::select('id', 'nombre', 'imagen_url')->get()->keyBy('id')->toArray();
    }
    public function actualizarIva($id, $qty)
    {
            $iva = Iva::find($id);
            if ($iva) {
    
                $iva->qty = $qty;
                $iva->save();
            }

            $this->iva = Iva::select('id', 'qty')->get()->keyBy('id')->toArray();
    }
    public function actualizarCaja($id, $name)
    {
            $caja = Caja::find($id);
            if ($caja) {
    
                $caja->name = $name;
                $caja->save();
            }

            $this->caja = Caja::select('id', 'name')->get()->keyBy('id')->toArray();
    }
    
    public function dropProducto($id)
    {
            $producto = Producto::find($id);
            $producto->delete();

            $this->productos = Producto::select('id', 'nombre', 'precio', 'imagen_url', 'iva_id', 'categoria_id', 'stock', 'se_vende')->with('categoria')->get()->keyBy('id')->toArray();
    }
    public function dropCategoria($id)
    {
            $categoria = Categoria::find($id);
            $categoria->delete();

            $this->categorias = Categoria::select('id', 'nombre', 'imagen_url')->get()->keyBy('id')->toArray();
    }
    public function dropIva($id)
    {
            $iva = Iva::find($id);
            $iva->delete();

            $this->iva = Iva::select('id', 'qty')->get()->keyBy('id')->toArray();
    }
    public function dropCaja($id)
    {
            $caja = Caja::find($id);
            $caja->delete();

            $this->caja = Caja::select('id', 'name')->get()->keyBy('id')->toArray();
    }
    public function render()
    {
        return view('livewire.gestion-invetario');
    }
}
