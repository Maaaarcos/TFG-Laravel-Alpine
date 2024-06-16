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
    try {
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

        // Actualizar la lista de productos
        $this->productos = Producto::select('id', 'nombre', 'precio', 'imagen_url', 'iva_id', 'categoria_id', 'stock', 'se_vende')
            ->with('categoria')
            ->get()
            ->keyBy('id')
            ->toArray();

        
        session()->flash('message', 'Producto creado correctamente');
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Manejar errores de validación
        $errors = $e->validator->errors()->getMessages();
        foreach ($errors as $field => $message) {
            session()->flash('error_' . $field, $this->getCustomErrorMessage($field, $message));
        }
    } catch (\Exception $e) {
        // Manejar cualquier otra excepción inesperada
        session()->flash('error', 'Ocurrió un error al crear el producto. Por favor, inténtelo de nuevo más tarde.');
    }
}

    public function crearCategoria()
{
    try {
        $this->validate($this->rulesCategoria);
        
        // Guardar la imagen de la categoría
        $imagenPath = $this->imagenCategoria->store('categorias', 'public');

        // Crear la categoría
        Categoria::create([
            'nombre' => $this->nombreCategoria,
            'imagen_url' => $imagenPath,
        ]);

        // Actualizar la lista de categorías
        $this->categorias = Categoria::select('id', 'nombre', 'imagen_url')
            ->get()
            ->keyBy('id')
            ->toArray();

        // Limpiar los campos del formulario después de crear la categoría
        $this->nombreCategoria = '';
        $this->imagenCategoria = null;

        session()->flash('message', 'Categoría creada correctamente');
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Manejar errores de validación
        $errors = $e->validator->errors()->getMessages();
        foreach ($errors as $field => $message) {
            session()->flash('error_' . $field, implode(', ', $message));
        }
    } catch (\Exception $e) {
        // Manejar cualquier otra excepción inesperada
        session()->flash('error', 'Ocurrió un error al crear la categoría. Por favor, inténtelo de nuevo más tarde.');
    }
}

public function crearEmpleado()
{
    try {
        // Validar los datos del formulario
        $this->validate($this->rulesEmpleado);

        // Asegurar que la imagen se ha subido correctamente
        if ($this->imagen_empleado && $this->imagen_empleado instanceof \Illuminate\Http\UploadedFile) {
            $imagenPath = $this->imagen_empleado->store('empleados', 'public');
        } else {
            throw new \Exception('Error al subir la imagen.');
        }

        // Crear un nuevo usuario
        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'privilegios' => $this->privilegios,
            'imagen_url' => $imagenPath,
            'puesto_empresa' => $this->puesto_empresa,
        ]);

        $this->reset([
            'name',
            'email',
            'password',
            'privilegios',
            'imagen_empleado',
            'puesto_empresa',
        ]);

        // Actualizar la lista de usuarios
        $this->user = User::select('id', 'name', 'email', 'password', 'privilegios', 'imagen_url', 'puesto_empresa')
            ->get()
            ->keyBy('id')
            ->toArray();

        // Mensaje de éxito
        session()->flash('message', 'Empleado creado correctamente');
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Manejar errores de validación
        $errors = $e->validator->errors()->getMessages();
        foreach ($errors as $field => $message) {
            session()->flash('error_' . $fields, implode(', ', $message));
        }
    } catch (\Illuminate\Database\QueryException $e) {
        // Manejar errores de la base de datos
        session()->flash('error', 'Hubo un problema con la base de datos. Por favor, inténtelo de nuevo más tarde.');
    } catch (\Exception $e) {
        // Manejar todos los demás errores
        session()->flash('error', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo más tarde.');
    }
}


/**
 * Get custom error message based on field and message
 */
private function getCustomErrorMessage($field, $message)
{
    $customMessages = [
        'name' => 'El nombre es obligatorio y debe tener al menos 3 caracteres.',
        'email' => 'El correo electrónico es obligatorio y debe ser una dirección de correo válida.',
        'password' => 'La contraseña es obligatoria y debe tener al menos 8 caracteres.',
        'privilegios' => 'Debe seleccionar los privilegios del usuario.',
        'imagen_empleado' => 'Debe subir una imagen válida para el empleado.',
        'puesto_empresa' => 'El puesto en la empresa es obligatorio.',
    ];

    return $customMessages[$field] ?? implode(', ', $message);
}

    public function crearIva($qty)
    {
        try {
            Iva::create(['qty' => $qty]);
            $this->iva = Iva::select('id', 'qty')->get()->keyBy('id')->toArray();
            session()->flash('message', 'IVA creado correctamente');
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Hubo un problema al crear el IVA. Por favor, inténtelo de nuevo más tarde.');
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo más tarde.');
        }
    }
    
    public function crearCaja($name)
    {
        try {
            Caja::create(['name' => $name]);
            $this->caja = Caja::select('id', 'name')->get()->keyBy('id')->toArray();
            session()->flash('message', 'Caja creada correctamente');
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Hubo un problema al crear la caja. Por favor, inténtelo de nuevo más tarde.');
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo más tarde.');
        }
    }
    

    public function actualizarEmpleado($id, $name, $email, $password, $privilegios, $imagen_url = null,$puesto_empresa)
    {
        try {
            $user = User::find($id);
            if ($user) {
                $user->name = $name;
                $user->email = $email;
                $user->password = bcrypt($password);
                $user->privilegios = $privilegios;
                $user->puesto_empresa = $puesto_empresa;
                
                if (!empty($password)) {
                    $user->password = bcrypt($password);
                }
                
                if ($this->imagen_empleado) {
                    $this->validate(['imagen_empleado' => 'image|max:2048']);
                    $imagenPath = $this->imagen_empleado->store('empleados', 'public');
                    $user->imagen_url = $imagenPath;
                }
    
                $user->save();
                session()->flash('message', 'Empleado actualizado correctamente');
            } else {
                session()->flash('error', 'Empleado no encontrado.');
            }
    
            $this->user = User::select('id', 'name', 'email', 'password', 'privilegios', 'imagen_url', 'puesto_empresa')->get()->keyBy('id')->toArray();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->getMessages();
            foreach ($errors as $field => $message) {
                session()->flash('error_' . $field, $this->getCustomErrorMessage($field, $message));
            }
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Hubo un problema al actualizar el empleado. Por favor, inténtelo de nuevo más tarde.');
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo más tarde.');
        }

    }

    public function actualizarProducto($id, $nombre, $precio, $iva_id, $categoria_id, $stock, $se_vende, $imagen_url = null)
    {
        try {
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
                session()->flash('message', 'Producto actualizado correctamente');
            } else {
                session()->flash('error', 'Producto no encontrado.');
            }
    
            $this->productos = Producto::select('id', 'nombre', 'precio', 'imagen_url', 'iva_id', 'categoria_id', 'stock', 'se_vende')
                ->with('categoria')
                ->get()
                ->keyBy('id')
                ->toArray();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->getMessages();
            foreach ($errors as $field => $message) {
                session()->flash('error_' . $field, $this->getCustomErrorMessage($field, $message));
            }
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Hubo un problema al actualizar el producto. Por favor, inténtelo de nuevo más tarde.');
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo más tarde.');
        }
    }
    
    public function actualizarCategoria($id, $nombre, $imagen_url = null)
    {
        try {
            $categoria = Categoria::find($id);
            if ($categoria) {
                $categoria->nombre = $nombre;
                $categoria->imagen_url = $imagen_url;
                $categoria->save();
                session()->flash('message', 'Categoria actualizada correctamente');
            } else {
                session()->flash('error', 'Categoría no encontrada.');
            }
    
            $this->categorias = Categoria::select('id', 'nombre', 'imagen_url')->get()->keyBy('id')->toArray();
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Hubo un problema al actualizar la categoría. Por favor, inténtelo de nuevo más tarde.');
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo más tarde.');
        }
    }
    
    public function actualizarIva($id, $qty)
    {
        try {
            $iva = Iva::find($id);
            if ($iva) {
                $iva->qty = $qty;
                $iva->save();
                session()->flash('message', 'IVA actualizado correctamente');
            } else {
                session()->flash('error', 'IVA no encontrado.');
            }
    
            $this->iva = Iva::select('id', 'qty')->get()->keyBy('id')->toArray();
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Hubo un problema al actualizar el IVA. Por favor, inténtelo de nuevo más tarde.');
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo más tarde.');
        }
    }
    
    public function actualizarCaja($id, $name)
    {
        try {
            $caja = Caja::find($id);
            if ($caja) {
                $caja->name = $name;
                $caja->save();
                session()->flash('message', 'Caja actualizada correctamente');
            } else {
                session()->flash('error', 'Caja no encontrada.');
            }
    
            $this->caja = Caja::select('id', 'name')->get()->keyBy('id')->toArray();
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Hubo un problema al actualizar la caja. Por favor, inténtelo de nuevo más tarde.');
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo más tarde.');
        }
    }
    
    public function dropEmpleado($id)
    {
        try {
            $user = User::find($id);
            if ($user) {
                $user->delete();
                session()->flash('message', 'Empleado eliminado correctamente');
            } else {
                session()->flash('error', 'Empleado no encontrado.');
            }
    
            $this->user = User::select('id', 'name', 'email', 'password', 'privilegios', 'imagen_url', 'puesto_empresa')->get()->keyBy('id')->toArray();
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Hubo un problema al eliminar el empleado. Por favor, inténtelo de nuevo más tarde.');
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo más tarde.');
        }
    }
    

public function dropProducto($id)
{
    try {
        $producto = Producto::find($id);
        if ($producto) {
            $producto->delete();
            session()->push('messages.success', 'Producto eliminado correctamente');
        } else {
            session()->push('messages.error', 'Producto no encontrado.');
        }

        // Actualizar la lista de productos después de la eliminación
        $this->productos = Producto::select('id', 'nombre', 'precio', 'imagen_url', 'iva_id', 'categoria_id', 'stock', 'se_vende')
            ->with('categoria')
            ->get()
            ->keyBy('id')
            ->toArray();
    } catch (\Illuminate\Database\QueryException $e) {
        session()->push('messages.error', 'Hubo un problema al eliminar el producto. Por favor, inténtelo de nuevo más tarde.');
    } catch (\Exception $e) {
        session()->push('messages.error', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo más tarde.');
    }
}

    
public function dropCategoria($id)
{
    try {
        $categoria = Categoria::find($id);
        if ($categoria) {
            $categoria->delete();
            session()->flash('message', 'Categoría eliminada correctamente');
            session()->flash('message_type', 'success');
        } else {
            session()->flash('message', 'Categoría no encontrada.');
            session()->flash('message_type', 'error');
        }

        // Actualizar la lista de categorías
        $this->categorias = Categoria::select('id', 'nombre', 'imagen_url')->get()->keyBy('id')->toArray();
    } catch (\Illuminate\Database\QueryException $e) {
        session()->flash('message', 'Hubo un problema al eliminar la categoría. Por favor, inténtelo de nuevo más tarde.');
        session()->flash('message_type', 'error');
    } catch (\Exception $e) {
        session()->flash('message', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo más tarde.');
        session()->flash('message_type', 'error');
    }
}




    
public function dropIva($id)
{
    try {
        $iva = Iva::find($id);
        if ($iva) {
            $iva->delete();
            session()->flash('message', 'IVA eliminado correctamente');
            session()->flash('message_type', 'success');
        } else {
            session()->flash('message', 'IVA no encontrado.');
            session()->flash('message_type', 'error');
        }

        $this->iva = Iva::select('id', 'qty')->get()->keyBy('id')->toArray();
    } catch (\Illuminate\Database\QueryException $e) {
        session()->flash('message', 'Hubo un problema al eliminar el IVA. Por favor, inténtelo de nuevo más tarde.');
        session()->flash('message_type', 'error');
    } catch (\Exception $e) {
        session()->flash('message', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo más tarde.');
        session()->flash('message_type', 'error');
    }
}

    
public function dropCaja($id)
{
    try {
        $caja = Caja::find($id);
        if ($caja) {
            $caja->delete();
            session()->flash('message', 'Caja eliminada correctamente');
            session()->flash('message_type', 'success');
        } else {
            session()->flash('message', 'Caja no encontrada.');
            session()->flash('message_type', 'error');
        }

        $this->caja = Caja::select('id', 'name')->get()->keyBy('id')->toArray();
    } catch (\Illuminate\Database\QueryException $e) {
        session()->flash('message', 'Hubo un problema al eliminar la caja. Por favor, inténtelo de nuevo más tarde.');
        session()->flash('message_type', 'error');
    } catch (\Exception $e) {
        session()->flash('message', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo más tarde.');
        session()->flash('message_type', 'error');
    }
}


    public function render()
    {
        return view('livewire.gestion-invetario');
    }
}
