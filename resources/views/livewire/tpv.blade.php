<div id="tpv" class="h-screen flex" x-data="{
        showModal: false,
        showCrearTarjeta: false,
        showComprobanteArqueo: false,
        showCrearArqueo: false,
        showInicioCaja: false,
        showSeleccionCaja: true,
        showTpv: true,
        showTR: false,
        showArqueo: false,
        showVentas: false,
        showOperacionVenta: false,
        showVales: false,
        showCambio: false,
        productos: @entangle('productos'),
        categorias: @entangle('categorias'),
        iva: @entangle('iva'),
        showModal: false,
        carrito: JSON.parse(localStorage.getItem('carrito')) || {},
        carritoEspera: JSON.parse(localStorage.getItem('carrito')) || {},
        totalCarrito: 0,
        totalSinDesglosar: 0,
        productosShow: [],
        carritoAdd(id) {
            // Verificar si el producto ya está en el carrito
            if (localStorage.getItem('carrito')) {
                let carrito = JSON.parse(localStorage.getItem('carrito'));
                if (carrito[id]) {
                    carrito[id].cantidad++;
                } else {
                    carrito[id] = {
                        cantidad: 1,
                        id: id,
                        name: this.productos[id].nombre,
                        precio: this.productos[id].precio,
                    };
                }
                localStorage.setItem('carrito', JSON.stringify(carrito));
            } else {
                // Si no hay ningún producto en el carrito, añadir el primer producto
                let carrito = {};
                carrito[id] = {
                    cantidad: 1,
                    id: id,
                    name: this.productos[id].nombre,
                    precio: this.productos[id].precio,
                };
                localStorage.setItem('carrito', JSON.stringify(carrito));
            }
        },
        dropCarrito(id) {
            if (this.carrito[id] !== undefined) {
                delete this.carrito[id];
            }
            this.calcularBase();
            localStorage.setItem('carrito', JSON.stringify(this.carrito));
        },
        deleteCarrito() {
            this.carrito = {};
            this.calcularBase();
            localStorage.setItem('carrito', JSON.stringify(this.carrito));
        },
        selectProd(id) {
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
        saveCarrito() {
            console.log(this.carritoEspera);
            this.carritoEspera.push(this.carrito);
            this.carrito = {};
            this.calcularBase();
            localStorage.setItem('carrito', JSON.stringify(this.carrito));
            {{-- localStorage.setItem('carritoEspera', JSON.stringify(this.carritoEspera)); --}}
        },
        calcularBase() {
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
        updateTotalSinDesglosar() {
            this.totalSinDesglosar = this.calcularIVA() + this.totalCarrito;
        },
        
    }" x-init="productosShow = productos;
    calcularBase();
    totalSinDesglosar = calcularIVA() + totalCarrito;
    setInterval(() => updateTotalSinDesglosar());
    $watch('carrito', () => updateTotalSinDesglosar());
    $watch('totalCarrito', () => updateTotalSinDesglosar());
    console.log(this.calcularIVA);">
        {{-- columna izquierda --}}
        <div class="bg-gray-800 text-white w-1/12 flex flex-col items-center">
            <div class="my-2 text-center cursor-pointer"
                @click=" showTpv = true; showTR = false; showArqueo = false; showVentas = false;">
                <i class="fa-solid fa-cash-register fa-3x px-4 pb-2 pt-4"></i>
                <p class="mx-2">TPV</p>
            </div>
            <div class="my-2 text-center cursor-pointer"
                @click=" showTpv = false; showTR = false; showArqueo = false; showVentas = true;">
                <i class="fa-solid fa-file-invoice fa-3x px-4 pb-2 pt-4"></i>
                <p class="mx-2">Ventas</p>
            </div>
            <div class="my-2 text-center cursor-pointer"
                @click=" showTpv = false; showTR = true; showArqueo = false; showVentas = false;">
                <i class="fa-solid fa-hand-holding-heart fa-3x px-4 pb-2 pt-4"></i>
                <p class="mx-2">Tarjetas Regalo</p>
            </div>
            <div class="my-2 text-center cursor-pointer"
                @click=" showTpv = false; showTR = false; showArqueo = true; showVentas = false;">
                <i class="fa-solid fa-folder-open fa-3x px-4 pb-2 pt-4"></i>
                <p class="mx-2">Arqueo</p>
            </div>
            <div class="my-2 text-center cursor-pointer">
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
                </form>
                <i class="fa-solid fa-sign-out fa-3x px-4 py-2 cursor-pointer" onclick="document.getElementById('logout-form').submit();"></i>
                <p class="mx-2">Salir</p>
            </div>
        </div>
        {{-- columna central --}}
        <div class="flex-1 flex flex-col w-8/12">
            {{-- navegador --}}
            <div class="bg-gray-600 text-white p-4 h-20 flex items-center">
                <i class="fa-solid fa-magnifying-glass fa-3x px-4 py-2 cursor-pointer"></i>
                <input type="text"
                    class="rounded-full px-4 py-2 w-full bg-white text-black border border-gray-300 focus:outline-none focus:border-blue-500">
            </div>
            {{-- categorias  --}}
            <div class="flex overflow-x-auto bg-skin-primary">
                @foreach($categorias as $categoria)
                    <div class="flex-none w-24 h-24 bg-gray-300 p-4 m-3 cursor-pointer" @click="selectProd({{ $categoria['id'] }})">
                        <p class="text-center mt-2">{{ $categoria['nombre'] }}</p>
                    </div>
                @endforeach
            </div>
            {{-- productos --}}
            <div class="flex-1 bg-gray-200 overflow-y-auto h-screen flex flex-wrap justify-start">
                @foreach($productos as $producto)
                    <div class="my-3 bg-white w-36 h-36 p-4 mx-4 flex flex-col justify-center items-center cursor-pointer"
                        x-data="{ id: {{ $producto['id'] }} }"
                        @click="carritoAdd(id)">
                        <img src="{{ $producto['imagen_url'] }}" alt="Foto" class="w-16 h-16 object-cover rounded-full">
                        <p class="text-center mt-2">{{ $producto['nombre'] }}</p>
                        <p class="text-center mt-2">{{ number_format($producto['precio'], 2) }}€</p>
                    </div>
                @endforeach
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
                                <td class="ml-4" x-text="articulo.precio + '€'"></td>
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
            <div class="bg-gray-600 text-white p-1 ">
                <div class="grid grid-cols-3 md:grid-cols-4 gap-4 md:gap-0">
                    <button @click="deleteCarrito" class="m-1 flex-grow items-center boton boton-danger !p-4 bg-red-800 rounded-lg">
                        <i class="fa-solid fa-trash  cursor-pointer"></i>
                    </button>
                    <button @click="saveCarrito" class="m-1 flex-grow items-center boton bg-gray-800 rounded-lg">
                        <i class="fa-solid fa-floppy-disk  py-2 cursor-pointer"></i>
                    </button>
                    <button @click="showModal = true; calcularBase();"
                        class="md:col-span-2 m-1 flex-grow items-center boton boton-success bg-blue-600 rounded-lg">
                        <span class="hidden md:inline">VENDER</span>
                        <i class="fa-solid fa-cart-shopping inline md:hidden" @click="showModal = true"></i>
                    </button>
                </div>
            </div>
        </div>
        {{-- Ventana emergente --}}
    <div class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex justify-center items-center"
        x-show="showModal" x-data="{
            showNumericKeyboard: true,
            showCardOptions: false,
            showMixedOptions: false,
            dineroEntregado: '',
            cambio: '',
            calcularCambio() {
                const total = totalCarrito + calcularIVA();
                const entregado = parseFloat(this.dineroEntregado);
                if (!isNaN(entregado)) {
                    this.cambio = (entregado - total).toFixed(2);
                } else {
                    this.cambio = '';
                }
            },
            deleteLastCharacter() {
                if (this.dineroEntregado.length > 0) {
                    this.dineroEntregado = this.dineroEntregado.slice(0, -1);
                    this.calcularCambio();
                }
            },
            clearInput() {
                this.dineroEntregado = '';
                this.calcularCambio();
            },
            pulsarTecla(tecla) {

                if (tecla === 'delete') {
                    this.deleteLastCharacter();
                } else if (tecla === 'cancel') {
                    this.clearInput();
                } else
                    if (tecla === 'b5')
                        {
                            this.dineroEntregado = (parseFloat(this.dineroEntregado) + 5).toString();
                        }
                        else if (tecla === 'b10')
                        {
                            this.dineroEntregado = (parseFloat(this.dineroEntregado) + 10).toString();
                        }
                        else if (tecla === 'b20')
                        {
                            this.dineroEntregado = (parseFloat(this.dineroEntregado) + 20).toString();
                        }
                        else if (tecla === 'b50')
                        {
                            this.dineroEntregado = (parseFloat(this.dineroEntregado) + 50).toString();
                        }
                else if (tecla === '.' && this.dineroEntregado.includes('.') === false)
                {
                    this.dineroEntregado += tecla;
                    this.calcularCambio();
                }
                else {
                    this.dineroEntregado += tecla;
                    this.calcularCambio();
                }
            }
        }" x-init="calcularCambio()">
        <div class="relative bg-white p-8 rounded-lg flex" style="width: 800px; height: 600px;">
            {{-- boton cerrar ventana --}}
            <button class="absolute top-0 right-0 p-2 rounded-tr-lg text-gray-600 hover:bg-red-600  "
                @click="showModal = false">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
            <!-- Sección metodos de pago -->
            <div class="w-2/5">
                <div>
                    <div class="mb-6 space-y-2">
                        <!-- Botones para seleccionar método de pago -->
                        <div>
                            <button
                                @click=" showNumericKeyboard = true; showCardOptions = false; showMixedOptions = false;"
                                class="relative border-b-2 border-black">

                                Efectivo
                            </button>
                        </div>
                        <div>
                            <button
                                @click=" showNumericKeyboard = false; showCardOptions = true; showMixedOptions = false;"
                                class="relative border-b-2 border-black">

                                Tarjeta
                            </button>
                        </div>
                        <div>
                            <button
                                @click=" showNumericKeyboard = false; showCardOptions = false; showMixedOptions = true;"
                                class="relative border-b-2 border-black">
                                Pago dividida
                            </button>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <ul>
                            <li class="border-b-2 border-l-skin-primary pb-2">Imp:</li>
                            <li class="border-b-2 border-l-skin-primary pb-2">Base:</li>
                            <li class="border-b-2 border-l-skin-primary pb-2">Cuota:</li>
                            <li class="border-b-2 border-l-skin-primary pb-2">Total:</li>
                        </ul>
                    </div>
                    <div>
                        <ul>
                            <li class="border-b-2 border-l-skin-primary pb-2" x-text="tipoIVA()"></li>
                            <li class="border-b-2 border-l-skin-primary pb-2" x-text="totalCarrito.toFixed(2) + '€'">
                            </li>
                            <li class="border-b-2 border-l-skin-primary pb-2" x-text="calcularIVA().toFixed(2) + '€'">
                            </li>
                            <li class="border-b-2 border-l-skin-primary pb-2"
                                x-text="(totalCarrito + calcularIVA()).toFixed(2) + '€'"></li>
                        </ul>
                    </div>
                </div>

            </div>

            <!-- Pago en Efectivo -->
            <div class=" w-3/5 m-0" x-show="showNumericKeyboard">

                <div class="grid grid-cols-2 gap-4">
                    {{-- Columna de datos --}}
                    <div>
                        <ul>
                            <li class="border-b-2 border-l-skin-primary pb-2">TOTAL:</li>
                            <li class="border-b-2 border-l-skin-primary pb-2">ENTREGADO:</li>
                            <li class="border-b-2 border-l-skin-primary pb-2">CAMBIO:</li>
                        </ul>
                    </div>
                    {{-- Columna de precios --}}
                    <div class="mb-6">
                        <ul>
                            <li class="border-b-2 border-l-skin-primary pb-2"
                                x-text="(totalCarrito + calcularIVA()).toFixed(2) + '€'">
                            </li>
                            <li class="border-b-2 border-l-skin-primary pb-2" x-text="dineroEntregado + '€'"></li>
                            <li class="border-b-2 border-l-skin-primary pb-2" x-text="cambio + '€'"></li>
                        </ul>
                    </div>
                </div>

                <!-- Teclado numerico -->
                <div class="grid grid-cols-6 gap-1 mb-6">
                    <button @click="pulsarTecla('7')" class="py-2  boton w-20 h-20 bg-blue-400 rounded-lg">7</button>
                    <button @click="pulsarTecla('8')" class="py-2  boton w-20 h-20 bg-blue-400 rounded-lg">8</button>
                    <button @click="pulsarTecla('9')" class="py-2  boton w-20 h-20 bg-blue-400 rounded-lg">9</button>
                    <button @click="pulsarTecla('cancel')" class="py-2  boton w-20 h-20 bg-blue-400 rounded-lg"><i
                            class="fa-solid fa-trash cursor-pointer"></i></button>
                    <button @click="pulsarTecla('b5')" class="py-2  boton  col-span-2 bg-blue-400 rounded-lg">5.00€</button>
                    <button @click="pulsarTecla('4')" class="py-2  boton w-20 h-20 bg-blue-400 rounded-lg">4</button>
                    <button @click="pulsarTecla('5')" class="py-2  boton w-20 h-20 bg-blue-400 rounded-lg">5</button>
                    <button @click="pulsarTecla('6')" class="py-2  boton w-20 h-20 bg-blue-400 rounded-lg">6</button>
                    <button @click="pulsarTecla('delete')" class="py-2  boton w-20 h-20 bg-blue-400 rounded-lg"><i
                            class="fa-solid fa-delete-left cursor-pointer"></i></button>
                    <button @click="pulsarTecla('b10')" class="py-2  boton   col-span-2 bg-blue-400 rounded-lg">10.00€</button>
                    <button @click="pulsarTecla('1')" class="py-2  boton w-20 h-20 bg-blue-400 rounded-lg">1</button>
                    <button @click="pulsarTecla('2')" class="py-2  boton w-20 h-20 bg-blue-400 rounded-lg">2</button>
                    <button @click="pulsarTecla('3')" class="py-2  boton w-20 h-20 bg-blue-400 rounded-lg">3</button>
                    <button @click="calcularCambio" class="py-2  boton  row-span-2"><i
                            class="fa-solid fa-arrow-turn-down transform rotate-90 bg-blue-400 rounded-lg"></i></button>
                    <button @click="pulsarTecla('b20')" class="py-2  boton  col-span-2 bg-blue-400 rounded-lg">20.00€</button>
                    <button class="py-2 boton"></button>
                    <button @click="pulsarTecla('.')" class="py-2  boton w-20 h-20 bg-blue-400 rounded-lg">.</button>
                    <button @click="pulsarTecla('0')" class="py-2  boton w-20 h-20 bg-blue-400 rounded-lg">0</button>
                    <button @click="pulsarTecla('b50')" class="py-2  boton col-span-2 bg-blue-400 rounded-lg">50.00€</button>
                </div>
                <div class="flex justify-between  mt-16">
                    <button class="boton boton-danger bg-red-600" @click="showModal = false">CANCELAR</button>
                    <button class="boton boton-success bg-green-200">PAGAR</button>
                </div>
            </div>
            {{-- Pago con Tarjeta --}}
            <div class="w-1/2 " x-show="showCardOptions">
                <div class="grid grid-cols-2 gap-4">
                    {{-- Columna de datos --}}
                    <div>
                        <ul>
                            <li class="border-b-2 border-l-skin-primary pb-2">TOTAL:</li>
                        </ul>
                    </div>
                    {{-- Columna de precios --}}
                    <div class="mb-6">
                        <ul>
                            <li class="border-b-2 border-l-skin-primary pb-2"
                                x-text="(totalCarrito + calcularIVA()).toFixed(2) + '€'"></li>
                        </ul>
                    </div>
                </div>
                <div>
                    <select class="form-select mt-1 block w-full">
                        <option selected disabled>Selecciona un banco</option>
                        <option value="banco1">Banco 1</option>
                        <option value="banco2">Banco 2</option>
                        <option value="banco3">Banco 3</option>
                    </select>
                </div>


                <div class="flex justify-between mt-16">
                    <button class="boton boton-danger" @click="showModal = false">CANCELAR</button>
                    <button class="boton boton-success">PAGAR</button>
                </div>
            </div>
            {{-- Pago Dividido --}}
            <div class="w-1/2 " x-show="showMixedOptions">
                <div class="grid grid-cols-2 gap-4">
                    {{-- Columna de datos --}}
                    <div>
                        <ul>
                            <li class="border-b-2 border-l-skin-primary pb-2">TOTAL:</li>
                            <li class="border-b-2 border-l-skin-primary pb-2">ENTREGADO:</li>
                            <li class="border-b-2 border-l-skin-primary pb-2">CAMBIO:</li>
                        </ul>
                    </div>
                    {{-- Columna de precios --}}
                    <div class="mb-6">
                        <ul>
                            <li class="border-b-2 border-l-skin-primary pb-2" x-text="totalCarrito + '€'"></li>
                            <li class="border-b-2 border-l-skin-primary pb-2" x-text="dineroEntregado + '€'"></li>
                            <li class="border-b-2 border-l-skin-primary pb-2" x-text="cambio + '€'"></li>
                        </ul>
                    </div>
                </div>

                <div class="flex justify-between mt-16">
                    <button class="boton boton-danger" @click="showModal = false">CANCELAR</button>
                    <button class="boton boton-success">PAGAR</button>
                </div>
            </div>
        </div>
    </div>
</div>