<div id="gestion" class="h-screen flex flex-col"
    x-data="{
        showInventario: true,
        showEmpleados: false,
        showCajas: false,
        showCategorias: false,
        showIvas: false,
        ventanaNuevoProducto: false,
        ventanaEditarProducto: false,
        ventanaNuevaCategoria: false,
        ventanaEditarCategoria: false,
        ventanaNuevoIva: false,
        ventanaEditarIva: false,
        ventanaNuevaCaja: false,
        ventanaEditarCaja: false,
        productos: @entangle('productos'),
        categorias: @entangle('categorias'),
        ivas: @entangle('iva'),
        productoFiltrado: @entangle('productoFiltrado'),
        nombreProd: '',
        precioProd: '',
        imagenProd: '',
        iva_idProd: '',
        categoria_idProd: '',
        stockProd: '',
        estadoProd: '',
        getIvaProd(ivaId) {
            let ivaProd = 0;

                ivaProd = this.ivas[ivaId].qty;

            return ivaProd;
        },
    }" x-init="
            $watch('productoFiltrado', value => {
                    nombreProd = value.nombre;
                    precioProd = value.precio;
                    iva_idProd = value.iva_id;
                    categoria_idProd = value.categoria_id;
                    stockProd = value.stock;
                    estadoProd = value.se_vende;
                    imagenProd = value.imagen_url;
            })">

    <!-- Navegador -->
    <div class="w-full bg-gray-100 flex justify-around fixed top-0 left-0 z-10">
        <div class="my-2 text-center cursor-pointer"
            @click="showInventario = true; showEmpleados = false; showCategorias = false; showCajas = false; showIvas = false;">
            <p class="mx-2">Inventario</p>
        </div>
        <div class="my-2 text-center cursor-pointer"
            @click="showInventario = false; showEmpleados = true; showCategorias = false; showCajas = false; showIvas = false;">
            <p class="mx-2">Empleados</p>
        </div>
        <div class="my-2 text-center cursor-pointer"
            @click="showInventario = false; showEmpleados = false; showCategorias = true; showCajas = false; showIvas = false;">
            <p class="mx-2">Categorias</p>
        </div>
        <div class="my-2 text-center cursor-pointer"
            @click="showInventario = false; showEmpleados = false; showCategorias = false; showCajas = false; showIvas = true;">
            <p class="mx-2">Ivas</p>
        </div>
        <div class="my-2 text-center cursor-pointer"
            @click="showInventario = false; showEmpleados = false; showCategorias = false; showCajas = true; showIvas = false;">
            <p class="mx-2">Cajas</p>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="flex-1 pt-16 w-full overflow-y-auto px-4 md:px-8">
        <div x-show="showInventario">
        <div class="container mx-auto py-4"> 
            <div>
                <button @click="ventanaNuevoProducto = true">
                    <i class="fa-solid fa-pen-to-square fa-3x px-4 pb-2 pt-4 mb-3 bg-blue-400"></i>
                </button>
            </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border">
                <thead>
                    <tr>
                    <th class="py-2 px-4 border-b">Imagen</th>
                        <th class="py-2 px-4 border-b">Nombre</th>
                        <th class="py-2 px-4 border-b">Precio</th>
                        <th class="py-2 px-4 border-b">IVA</th>
                        <th class="py-2 px-4 border-b">Categoria</th>
                        <th class="py-2 px-4 border-b">Stock</th>
                        <th class="py-2 px-4 border-b">Estado</th>
                        <th class="py-2 px-4 border-b"></th>
                        <th class="py-2 px-4 border-b"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="producto in productos" :key="producto.id">
                        <tr>
                            <td class="py-2 px-4 border-b">
                                <img :src="producto.imagen_url" alt="Foto del producto" class="h-12 w-12 object-cover">
                            </td>
                            <td class="py-2 px-4 border-b" x-text="producto.nombre"></td>
                            <td class="py-2 px-4 border-b" x-text="producto.precio.toFixed(2) + '€'"></td>
                            <td class="py-2 px-4 border-b" x-text="getIvaProd(producto.iva_id) + '%'"></td>
                            <td class="py-2 px-4 border-b" x-text="categorias[producto.categoria_id].nombre"></td>
                            <td class="py-2 px-4 border-b" x-text="producto.stock"></td>
                            <td class="py-2 px-4 border-b">
                                <span class="inline-block h-4 w-4 rounded-full"
                                    :class="producto.se_vende ? 'bg-green-500' : 'bg-red-500'"></span>
                            </td>
                            <td class="py-2 px-4 border-b">
                                <button @click="$wire.getProducto(producto.id); ventanaEditarProducto= true;" class="text-blue-500">Editar</button>
                            </td>
                            <td class="py-2 px-4 border-b">
                                <button @click="$wire.dropProducto(producto.id);" class="text-blue-500">Borrar</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="ventanaEditarProducto" x-data="{
    }" class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-8 rounded-lg flex flex-col">
            <div class="uppercase text-xl font-bold mb-4">
                Editcion Producto
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" id="nombreProd" name="nombreProd" x-model="nombreProd" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Precio</label>
                    <input type="text" id="precioProd" name="precioProd" x-model="precioProd" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">IVA</label>
                    <select x-model="iva_idProd" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <option value="" disabled selected>Seleccione IVA</option>
                        <template x-for="iva in ivas" :key="iva.id">
                            <option :value="iva.id" x-text="iva.qty + '%'"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Categoría</label>
                    <select x-model="categoria_idProd" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <option value="" disabled selected>Seleccione Categoría</option>
                        <template x-for="categoria in categorias" :key="categoria.id">
                            <option :value="categoria.id" x-text="categoria.nombre"></option>
                        </template>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Stock</label>
                    <input type="text" id="stockProd" name="stockProd" x-model="stockProd" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <select x-model="estadoProd" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <option value="" disabled selected>Seleccione Estado</option>
                        <option value="1">Habilitado</option>
                        <option value="0">Deshabilitado</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Imagen</label>
                    <input type="text" id="imagenProd" name="imagenProd" x-model="imagenProd" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>
            <div class="flex justify-end">
                <button class="boton" @click="
                    $wire.actualizarProducto(nombreProd, precioProd, iva_idProd, categoria_idProd, stockProd, estadoProd, imagenProd);
                    ventanaEditarProducto = false;
                ">Guardar</button>
            </div>
        </div>
    </div>
    
    <div x-show="ventanaNuevoProducto" x-data="{
            nombre: '',
            precio: '',
            imagen: '',
            iva_id: '',
            categoria_id: '',
            stock: '',
            estado: '',
            getProductoData() {
                return {
                    nombre: this.nombre,
                    precio: parseFloat(this.precio),
                    iva_id: parseInt(this.iva_id),
                    categoria_id: parseInt(this.categoria_id),
                    stock: parseInt(this.stock),
                    estado: parseInt(this.estado),
                    imagen: this.imagen,
                    tipoNombre: typeof this.nombre,
                    tipoPrecio: typeof parseFloat(this.precio),
                    tipoIvaId: typeof parseInt(this.iva_id),
                    tipoCategoriaId: typeof parseInt(this.categoria_id),
                    tipoStock: typeof parseInt(this.stock),
                    tipoEstado: typeof parseInt(this.estado),
                    tipoImagen: typeof this.imagen
                };
            }
    }" class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex justify-center items-center">
            <div class="bg-white p-8 rounded-lg flex flex-col">
                <div class="uppercase text-xl font-bold mb-4">
                    Nuevo Producto
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre*</label>
                        <input type="text" id="nombre" name="nombre" x-model="nombre" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Precio*</label>
                        <input type="text" id="precio" name="precio" x-model="precio" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">IVA</label>
                        <select x-model="iva_id" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="" disabled selected>Seleccione IVA</option>
                            <template x-for="iva in ivas" :key="iva.id">
                                <option :value="iva.id" x-text="iva.qty + '%'"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Categoría</label>
                        <select x-model="categoria_id" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="" disabled selected>Seleccione Categoría</option>
                            <template x-for="categoria in categorias" :key="categoria.id">
                                <option :value="categoria.id" x-text="categoria.nombre"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Stock</label>
                        <input type="text" id="stock" name="stock" x-model="stock" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                        <select x-model="estado" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="">Seleccione Estado</option>    
                            <option value="1">Habilitado</option>
                            <option value="0">Deshabilitado</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Imagen</label>
                        <input type="text" id="imagen" name="imagen" x-model="imagen" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button class="boton" @click="
                        const data = getProductoData();
                        console.log(data);
                        $wire.crearProducto(data.nombre, data.precio, data.iva_id, data.categoria_id, data.stock, data.estado, data.imagen);
                        ventanaNuevoProducto = false;
                    ">Guardar</button>
                </div>
            </div>
    </div>   
</div>

    <div x-show="ventanaNuevaCaja" x-data="{
            nombre: '',
            precio: '',
            imagen: '',
            iva_id: '',
            categoria_id: '',
            stock: '',
            estado: '',
            getProductoData() {
                return {
                    nombre: this.nombre,
                    precio: parseFloat(this.precio),
                    iva_id: parseInt(this.iva_id),
                    categoria_id: parseInt(this.categoria_id),
                    stock: parseInt(this.stock),
                    estado: parseInt(this.estado),
                    imagen: this.imagen,
                    tipoNombre: typeof this.nombre,
                    tipoPrecio: typeof parseFloat(this.precio),
                    tipoIvaId: typeof parseInt(this.iva_id),
                    tipoCategoriaId: typeof parseInt(this.categoria_id),
                    tipoStock: typeof parseInt(this.stock),
                    tipoEstado: typeof parseInt(this.estado),
                    tipoImagen: typeof this.imagen
                };
            }
    }" class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex justify-center items-center">
            <div class="bg-white p-8 rounded-lg flex flex-col">
                <div class="uppercase text-xl font-bold mb-4">
                    Nuevo Producto
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre*</label>
                        <input type="text" id="nombre" name="nombre" x-model="nombre" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Precio*</label>
                        <input type="text" id="precio" name="precio" x-model="precio" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">IVA</label>
                        <select x-model="iva_id" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="" disabled selected>Seleccione IVA</option>
                            <template x-for="iva in ivas" :key="iva.id">
                                <option :value="iva.id" x-text="iva.qty + '%'"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Categoría</label>
                        <select x-model="categoria_id" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="" disabled selected>Seleccione Categoría</option>
                            <template x-for="categoria in categorias" :key="categoria.id">
                                <option :value="categoria.id" x-text="categoria.nombre"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Stock</label>
                        <input type="text" id="stock" name="stock" x-model="stock" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                        <select x-model="estado" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="">Seleccione Estado</option>    
                            <option value="1">Habilitado</option>
                            <option value="0">Deshabilitado</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Imagen</label>
                        <input type="text" id="imagen" name="imagen" x-model="imagen" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button class="boton" @click="
                        const data = getProductoData();
                        console.log(data);
                        $wire.crearProducto(data.nombre, data.precio, data.iva_id, data.categoria_id, data.stock, data.estado, data.imagen);
                        ventanaNuevoProducto = false;
                    ">Guardar</button>
                </div>
            </div>
    </div>

    <div x-show="showEmpleados">
            
    </div>

    <div x-show="showCajas">
            
    </div>

    <div x-show="showCategorias">
        <div>
            <button @click="ventanaNuevaCategoria = true">
                <i class="fa-solid fa-pen-to-square fa-3x px-4 pb-2 pt-4 mb-3 bg-blue-400"></i>
            </button>
        </div>
        <table class="min-w-full bg-white border">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Imagen</th>
                        <th class="py-2 px-4 border-b">Nombre</th>
                        <th class="py-2 px-4 border-b"></th>
                        <th class="py-2 px-4 border-b"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="categoria in categorias" :key="categoria.id">
                        <tr>
                            <td class="py-2 px-4 border-b">
                                <img :src="categoria.imagen_url" alt="Foto del producto" class="h-12 w-12 object-cover">
                            </td>
                            <td class="py-2 px-4 border-b" x-text="categoria.nombre"></td>
                            <td class="py-2 px-4 border-b">
                                <button @click="$wire.getCategoria(categoria.id);" class="text-blue-500">Editar</button>
                            </td>
                            <td class="py-2 px-4 border-b">
                                <button @click="$wire.dropCategoria(categoria.id);" class="text-blue-500">Borrar</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
        </table>

        <div x-show="ventanaNuevaCategoria" x-data="{
            nombre: '',
            imagen: ''
    }" class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-8 rounded-lg flex flex-col">
            <div class="uppercase text-xl font-bold mb-4">
                Nuevo Categoria
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre*</label>
                    <input type="text" id="nombre" name="nombre" x-model="nombre" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Imagen</label>
                    <input type="text" id="imagen" name="imagen" x-model="imagen" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>
            <div class="flex justify-end">
                <button class="boton" @click="
                    $wire.crearCategoria(nombre, imagen);
                    ventanaNuevaCategoria = false;
                ">Guardar</button>
            </div>
        </div>
        </div>
    </div>
    
    <div x-show="showIvas">
        <div>
            <button @click="ventanaNuevoIva = true">
                <i class="fa-solid fa-pen-to-square fa-3x px-4 pb-2 pt-4 mb-3 bg-blue-400"></i>
            </button>
        </div>
        <table class="min-w-full bg-white border">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">QTY</th>
                        <th class="py-2 px-4 border-b"></th>
                        <th class="py-2 px-4 border-b"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="iva in ivas" :key="iva.id">
                        <tr>
                            <td class="py-2 px-4 border-b" x-text="iva.qty + '%'"></td>
                            <td class="py-2 px-4 border-b">
                                <button @click="$wire.getIva(iva.id);" class="text-blue-500">Editar</button>
                            </td>
                            <td class="py-2 px-4 border-b">
                                <button @click="$wire.dropIva(iva.id);" class="text-blue-500">Borrar</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
        </table>
        <div x-show="ventanaNuevoIva" x-data="{
            qty: '',
            imagen: '',
    }" class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex justify-center items-center">
            <div class="bg-white p-8 rounded-lg flex flex-col">
                <div class="uppercase text-xl font-bold mb-4">
                    Nuevo IVA
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">QTY*</label>
                        <input type="text" id="qty" name="qty" x-model="qty" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button class="boton" @click="
                        $wire.crearIva(qty);
                        ventanaNuevoIva = false;
                    ">Guardar</button>
                </div>
            </div>
    </div>

    </div>
    </div>
</div>
