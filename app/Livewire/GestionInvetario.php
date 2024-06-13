<?php

namespace App\Livewire;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Iva;
use App\Models\Caja;
use App\Models\User;
use Livewire\WithFileUploads;
use Livewire\Component;

class GestionInvetario extends Component
{
    use WithFileUploads;

    // Propiedades comunes
    public $productos = [];
    public $categorias = [];
    public $iva = [];
    public $caja = [];
    public $user = [];

    // Propiedades de Producto
    public $nombre;
    public $precio;
    public $imagen;
    public $iva_id;
    public $categoria_id;
    public $stock;
    public $estado;

    // Propiedades de Categoria
    public $nombreCategoria;
    public $imagenCategoria;

    // Propiedades de User
    public $name;
    public $email;
    public $password;
    public $privilegios;
    public $puesto_empresa;
    public $imagen_empleado;

    protected $rules = [
        'nombre' => 'required|string',
        'precio' => 'required|numeric',
        'imagen' => 'required|image|max:2048', // 2MB Max
        'iva_id' => 'required|integer',
        'categoria_id' => 'required|integer',
        'stock' => 'required|integer',
        'estado' => 'required|boolean',
    ];

    protected $rulesCategoria = [
        'nombreCategoria' => 'required|string',
        'imagenCategoria' => 'required|image|max:2048', // 2MB Max
    ];

    protected $rulesEmpleado = [
        'name' => 'required|string',
        'email' => 'required|email',
        'password' => 'required|string',
        'privilegios' => 'required|integer',
        'imagen_empleado' => 'required|image|max:2048',
        'puesto_empresa' => 'required|string',
    ];

    public function mount()
    {
        $this->productos = Producto::select('id', 'nombre', 'precio', 'imagen_url', 'iva_id', 'categoria_id', 'stock', 'se_vende')->with('categoria')->get()->keyBy('id')->toArray();
        $this->categorias = Categoria::select('id', 'nombre', 'imagen_url')->get()->keyBy('id')->toArray();
        $this->iva = Iva::select('id', 'qty')->get()->keyBy('id')->toArray();
        $this->caja = Caja::select('id', 'name')->get()->keyBy('id')->toArray();
        $this->user = User::select('id', 'name', 'email', 'puesto_empresa', 'privilegios', 'password', 'imagen_url')->get()->keyBy('id')->toArray();
    }

    public function crearProducto()
    {
        $this->validate();

        // Guardar la imagen
        $imagenPath = $this->imagen->store('productos', 'public');

        // Crear el producto
        Producto::create([
            'nombre' => $this->nombre,
            'precio' => $this->precio,
            'imagen_url' => $imagenPath,
            'iva_id' => $this->iva_id,
            'categoria_id' => $this->categoria_id,
            'stock' => $this->stock,
            'estado' => $this->estado,
        ]);

        $this->productos = Producto::select('id', 'nombre', 'precio', 'imagen_url', 'iva_id', 'categoria_id', 'stock', 'se_vende')->with('categoria')->get()->keyBy('id')->toArray();
        $this->nombre = ''; $this->precio = ''; $this->iva_id = ''; $this->categoria_id = ''; $this->stock = ''; $this->estado = ''; $this->imagen = null;
    }

    public function crearCategoria()
    {
        $this->validate($this->rulesCategoria);
        
        $imagenPath = $this->imagenCategoria->store('categorias', 'public');

        Categoria::create([
            'nombre' => $this->nombreCategoria,
            'imagen_url' => $imagenPath,
        ]);

        $this->categorias = Categoria::select('id', 'nombre', 'imagen_url')->get()->keyBy('id')->toArray();
        $this->nombreCategoria = ''; $this->imagenCategoria = null;
    }

    public function crearEmpleado() 
    {
        $this->validate($this->rulesEmpleado);

        $imagenPath = $this->imagen_empleado->store('empleados', 'public');

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'privilegios' => $this->privilegios,
            'imagen_url' => $imagenPath,
            'puesto_empresa' => $this->puesto_empresa,
        ]);

        $this->user = User::select('id', 'name', 'email', 'password', 'privilegios', 'imagen_url', 'puesto_empresa')->get()->keyBy('id')->toArray();
    }

    public function actualizarEmpleado($id, $name, $email, $password, $privilegios, $imagen_empleado, $puesto_empresa)
    {
        
        $user = User::find($id);
        if ($user) {
            $user->name = $name;
            $user->email = $email;
            $user->password = bcrypt($password);
            $user->privilegios = $privilegios;
            $user->puesto_empresa = $puesto_empresa;

            if ($this->imagen_empleado) {
                $this->validate(['imagen_empleado' => 'image|max:2048']);
                $imagenPath = $this->imagen_empleado->store('empleados', 'public');
                $user->imagen_url = $imagenPath;
            }

            $user->save();
        }

        $this->user = User::select('id', 'name', 'email', 'password', 'privilegios', 'imagen_url', 'puesto_empresa')->get()->keyBy('id')->toArray();

    }

    public function crearIva($qty)
    {
        Iva::create(['qty' => $qty]);
        $this->iva = Iva::select('id', 'qty')->get()->keyBy('id')->toArray();
    }

    public function crearCaja($name)
    {
        Caja::create(['name' => $name]);
        $this->caja = Caja::select('id', 'name')->get()->keyBy('id')->toArray();
    }

    public function actualizarProducto($id, $nombre, $precio, $iva_id, $categoria_id, $stock, $se_vende, $imagen_url = null, $nueva_imagen = null)
    {
        $producto = Producto::find($id);
        if ($producto) {
            $producto->nombre = $nombre;
            $producto->precio = $precio;
            $producto->iva_id = $iva_id;
            $producto->categoria_id = $categoria_id;
            $producto->stock = $stock;
            $producto->se_vende = $se_vende;

            if ($this->imagen) {
                $this->validate(['imagen' => 'image|max:2048']);
                $imagenPath = $this->imagen->store('productos', 'public');
                $producto->imagen_url = $imagenPath;
            }

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

    public function dropEmpleado($id)
    {
        $user = User::find($id);
        $user->delete();

        $this->user = User::select('id', 'name', 'email', 'password', 'privilegios', 'imagen_url', 'puesto_empresa')->get()->keyBy('id')->toArray();
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
