<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @vite('resources/css/app.css')
</head>
<body>
<div id="tpv" class=" h-screen flex" 
x-data="{
    showModal: false,
    barcode: '',
    productosShow: null,
    productos: @entangle('productos'),
    categorias: @entangle('categorias'),
    iva: @entangle('iva'),
    carrito: JSON.parse(localStorage.getItem('carrito')) || {},
    carritoEspera: JSON.parse(localStorage.getItem('carrito')) || {},
    totalCarrito: 0,
    carritoAdd: function(id) {
        if (this.carrito[id] !== undefined) {
            this.carrito[id].cantidad++;
        } else {
            this.carrito[id] = {
                cantidad: 1,
                id: this.productos[id].id,
                name: this.productos[id].name,
                precio: this.productos[id].precio,

            }
            console.log(this.carrito);
        }
        this.calcularBase();
        localStorage.setItem('carrito', JSON.stringify(this.carrito));
    },
    dropCarrito: function(id) {
        if (this.carrito[id] !== undefined) {
            delete this.carrito[id];
        }
        this.calcularBase();
        localStorage.setItem('carrito', JSON.stringify(this.carrito));
    },
    deleteCarrito: function() {
        this.carrito = {};
        this.calcularBase();
        localStorage.setItem('carrito', JSON.stringify(this.carrito));
    },
    selectProd: function(id) {
        let arr = [];
        this.productos.forEach(function(prod) {
            prod.categorias.forEach(function(cat) {
                if (cat.id === id) {
                    arr.push(prod);
                }
            })
        });
        this.productosShow = arr;
    },
    saveCarrito: function() {
        console.log(this.carritoEspera);

        this.carritoEspera.push(this.carrito);

        this.carrito = {};
        this.calcularBase();

        localStorage.setItem('carrito', JSON.stringify(this.carrito));
        {{-- localStorage.setItem('carritoEspera', JSON.stringify(this.carritoEspera)); --}}
    },
    calcularBase: function() {
        let total = 0;
        for (let art in this.carrito) {
            if (this.carrito.hasOwnProperty(art)) {
                total += this.carrito[art].cantidad * this.carrito[art].precio;
            }
        }
        this.totalCarrito = total;
        localStorage.setItem('totalCarrito', JSON.stringify(total));
    },
    calcularIVA() {
        let precioIVA = 0;
        for (let art in this.carrito) {
            if (this.carrito.hasOwnProperty(art)) {
                precioIVA += ((this.carrito[art].cantidad * this.carrito[art].precio) * (this.iva[this.productos[art].iva_id].qty * 0.01));
            }
        }

        return precioIVA;
    },
    tipoIVA() {
        let IVA = [];
        for (let art in this.carrito) {
            console.log(art.valorIva);
            if (this.carrito.hasOwnProperty(art)) {
                IVA[this.iva[this.productos[art].iva_id].qty] += art.valorIva;
            }
        }
        {{-- console.log(IVA); --}}
        return IVA;
    },
}" x-init="productosShow = productos;
calcularBase();
console.log(this.productos);
">
    {{-- columna izquierda --}}
    <div class="bg-gray-800 text-white w-1/12 flex flex-col items-center">
        <div class="my-2">
            <i class="fa-solid fa-barcode fa-3x px-4 pb-2 pt-4"></i>
            <p class="mx-2">Nombre 1</p>
    </div>
        <div class="my-2">
            <i class="fa-solid fa-barcode fa-3x px-4 pb-2 pt-4"></i>
            <p class="mx-2">Nombre 2</p>
        </div>
        <div class="my-2">
            <i class="fa-solid fa-barcode fa-3x px-4 pb-2 pt-4"></i>
            <p class="mx-2">Nombre 3</p>
        </div>
        <div class="my-2">
            <i class="fa-solid fa-barcode fa-3x px-4 pb-2 pt-4"></i>
            <p class="mx-2">Nombre 4</p>
        </div>
        <div class="my-2">
            <i class="fa-solid fa-barcode fa-3x px-4 pb-2 pt-4"></i>
            <p class="mx-2">Nombre 5</p>
        </div>
    </div>
    {{-- columna central --}}
    <div class="flex-1 flex flex-col w-8/12">
        {{-- navegador --}}
        <div class="bg-gray-600 text-white p-4 h-20 flex items-center">
            <i class="fa-solid fa-magnifying-glass fa-3x px-4 py-2 cursor-pointer"></i>
            <i class="fa-solid fa-barcode fa-3x px-4 py-2 cursor-pointer"></i>
            <input type="text"
                class="rounded-full px-4 py-2 w-full bg-white text-black border border-gray-300 focus:outline-none focus:border-blue-500">
        </div>
        {{-- categorias  --}}
        <div class="flex overflow-x-auto bg-skin-primary">
            <template x-for="categoria in categorias">
                <div class="flex-none w-24 h-24 bg-gray-300 p-4 m-3 cursor-pointer" @click="selectProd(categoria.id)">
                    <p class="text-center mt-2" x-text='categoria.name'></p>
                </div>
            </template>
        </div>
        {{-- productos --}}
        <div class="flex-1 bg-gray-200 overflow-y-auto h-screen flex flex-wrap justify-start">
            <template x-for="producto in productos">
                <div class="my-3 bg-white w-36 h-36 p-4 mx-4 flex flex-col justify-center items-center cursor-pointer"
                    @click="carritoAdd(producto.id)">
                    <img src="producto.imagen_url" alt="Foto" class="w-16 h-16 object-cover rounded-full">
                    <p class="text-center mt-2" x-text='producto.nombre'></p>
                    <p class="text-center mt-2" x-text="(producto.precio).toFixed(2) + '€'"></p>
                </div>
            </template>
        </div>
    </div>
    {{-- columna derecha --}}
    <div class="bg-gray-600 text-white w-3/12 flex flex-col h-screen">
        <div class=" text-white p-4 h-20 ml-auto flex items-center">
            <i class="fa-regular fa-user fa-3x px-4 py-2 cursor-pointer "></i>
        </div>
        <div class="flex-1 bg-white border-l-4 border-gray-500 overflow-y-auto ">
            <table class="table-auto table-list">
                <thead class="text-black">
                    <tr class="">
                        <th class="border-r border-black">CANT</th>
                        <th class="col-span-2">NOMBRE</th>
                        <th>SUB.TOTAL</th>
                        <th class="ml-4">TOTAL</th>
                        <th class="border-l border-black"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="articulo in carrito">
                        <tr class="bg-slate-200 text-black text-sm">
                            <td class="border-r border-black" x-text="articulo.cantidad"></td>
                            <td class="col-span-2" x-text="articulo.name"></td>
                            <td class="ml-4" x-text="(articulo.precio).toFixed(2) + '€'"></td>
                            <td x-text="(articulo.precio * articulo.cantidad).toFixed(2) + '€'"></td>
                            <td class="border-l border-black" @click="dropCarrito(articulo.id)">
                                <i class="fa-solid fa-trash cursor-pointer text-red-600"></i>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

        </div>
        <div class="grid grid-cols-2 gap-4 pl-3 bg-gray-500 rounded-tl-xl rounded-tr-xl">
            {{-- Columna de datos --}}
            <div>
                <ul>
                    <li class=" pb-2">BASE IMPONIBLE:</li>
                    <li class=" pb-2">IVA:</li>
                    <li class=" pb-2">TOTAL:</li>
                </ul>
            </div>
            {{-- Columna de precios --}}
            <div class="mb-6">
                <ul>
                    <li class=" pb-2" x-text="totalCarrito.toFixed(2) + '€'"></li>
                    <li class=" pb-2" x-text="calcularIVA().toFixed(2) + '€'"></li>
                    <li class=" pb-2" x-text="(totalCarrito + calcularIVA()).toFixed(2) + '€'"></li>
                </ul>
            </div>
        </div>
    </div>
</div>
</body>
</html>