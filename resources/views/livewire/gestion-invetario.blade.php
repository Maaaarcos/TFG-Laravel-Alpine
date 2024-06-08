<div id="gestion" class="h-screen flex flex-col"
    x-data="{
        showInventario: true,
        showEmpleados: false,
        ventanaNuevoProducto: false,
        productos: @entangle('productos'),
        categorias: @entangle('categorias'),
        ivas: @entangle('iva'),
        name: '',
        precio: '',
        iva: '',
        categoria: '',
        stock: '',
        estado: '',
        getIvaProd(ivaId) {
            let ivaProd = 0;

            console.log(ivaId);
                ivaProd = this.ivas[ivaId].qty;

            return ivaProd;
        }
    }">

    <!-- Navegador -->
    <div class="w-full bg-gray-100 flex justify-around fixed top-0 left-0 z-10">
        <div class="my-2 text-center cursor-pointer"
            @click="showInventario = true; showEmpleados = false;">
            <p class="mx-2">Inventario</p>
        </div>
        <div class="my-2 text-center cursor-pointer"
            @click="showInventario = false; showEmpleados = true;">
            <p class="mx-2">Empleados</p>
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
                    <th class="py-2 px-4 border-b"></th>
                    <th class="py-2 px-4 border-b">Nombre</th>
                    <th class="py-2 px-4 border-b">Precio</th>
                    <th class="py-2 px-4 border-b">IVA</th>
                    <th class="py-2 px-4 border-b">Categoria</th>
                    <th class="py-2 px-4 border-b">Stock</th>
                    <th class="py-2 px-4 border-b">Estado</th>
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
                            <button @click="editProducto(producto)" class="text-blue-500">Editar</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>

            <div x-show="ventanaNuevoProducto" class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex justify-center items-center">
                <div class="bg-white p-8 rounded-lg flex flex-col" style="height: 300px; width: 500px">
                <div class=" uppercase text-xl font-bold mb-4">
                    Nuevo Producto
                </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre*</label>
                            <input type="text" id="name" name="name" x-model="name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Precio*</label>
                            <input type="text" id="precio" name="precio" x-model="precio" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">IVA</label>
                            <input type="text" id="iva" name="iva" x-model="iva" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Categoria</label>
                            <input type="text" id="categoria" name="categoria" x-model="categoria" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stock</label>
                            <input type="text" id="stock" name="stock" x-model="stock" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado</label>
                            <input type="text" id="estado" name="estado" x-model="estado" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Imagen</label>
                            <input type="text" id="imagen" name="imagen" x-model="imagen" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button class="boton" @click="crearProducto(name, precio, iva, categoria, stock, estado)">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        <div x-show="showEmpleados">
            <!-- Aquí puedes colocar la vista para Empleados -->
        </div>
    </div>
</div>
