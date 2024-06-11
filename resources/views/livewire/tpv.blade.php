<div id="tpv" class=" h-screen flex" x-data="{
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
    esVisible: false,
    VentanaRetiradaDinero: false,
    showVales: false,
    showCambio: false,
    showNumericKeyboard : false,
    showSala : false,
    showCardOptions : false,
    fecha: new Date(),
    barcode: '',
    search: '',
    searchCategory: '',
    productos: @entangle('productos'),
    productosShow: null,
    categorias: @entangle('categorias'),
    iva: @entangle('iva'),
    provincia: @entangle('provincia'),
    factura: @entangle('factura'),
    getNextRef: @entangle('getNextRef'),
    arqueos: @entangle('arqueos'),
    usuarios: @entangle('usuarios').defer,
    usuario: '',
    cajas: @entangle('cajas'),
    tarjeta_regalo: @entangle('tarjeta_regalo'),
    selectCaja: @entangle('selectCaja'),
    saldoInicial: @entangle('saldoInicial'),
    saldoTR: @entangle('saldoTR'),
    facturas: @entangle('facturas'),
    clonFacturas: @entangle('facturas'),
    pagos: @entangle('pagos'),
    terceros: @entangle('terceros'),
    conf: @entangle('conf').defer,
    valores_fijos_TR: @entangle('valores_fijos_TR'),
    movimientos_arqueo: @entangle('movimientos_arqueo'),
    lineas_factura: @entangle('lineas_factura'),
    prodFiltrados: @entangle('prodFiltrados'),
    historialDev: @entangle('historialDev'),
    cajaSeleccionada: '',
    carrito: JSON.parse(localStorage.getItem('carrito')) || {},
    carritoEspera: JSON.parse(localStorage.getItem('carritoEspera')) || {},
    arrayDevoluciones: {},
    arrayNuevaVenta: {},
    totalCarrito: 0,
    saldoTotal: 0,
    totalSinDesglosar: 0,
    dineroRetirado: 0,
    saldoEsperado: 100,
    saldoInicialSiguiente: 100,
    codigoBusqueda: '',
    codigoBusquedaTR: '',
    codigoBusquedaArqueo: '',
    referenciaVenta: '',
    operacionVenta: '',
    mensaje: '',
    stock: true,
    billetes: { '500': 0, '200': 0, '100': 0, '50': 0, '20': 0, '10': 0, '5': 0, '2': 0, '1': 0, '05': 0, '02': 0, '01': 0, '005': 0, '002': 0, '001': 0 },
    infoEmpresa() {
        let precioIVA = 0;
        for (let art in this.carrito) {
            if (this.carrito.hasOwnProperty(art)) {
                precioIVA += ((this.carrito[art].cantidad * this.carrito[art].precio) * (this.iva[this.productos[art].iva_id].qty * 0.01));
            }
        }
        return precioIVA;
    },
    carritoAdd: function(id) {
        if (this.carrito[id] !== undefined) {
            this.carrito[id].cantidad++;
        } else {
            this.carrito[id] = {
                cantidad: 1,
                id: this.productos[id].id,
                name: this.productos[id].nombre,
                precio: this.productos[id].precio,
            }
        }
        console.log(this.productos[id].categoria_id);
        this.calcularBase();
        localStorage.setItem('carrito', JSON.stringify(this.carrito));
    },
    productoExiste(codigo) {
        let producto_id = this.buscarPorCB(codigo);
        console.log(this.lineas_factura);
        console.log(producto_id);
        for (let linea of this.lineas_factura) {
            if (linea.producto_id === producto_id) {
                return true;
            }
        }
        return false;
    },
    devolucionesAdd(id) {
        if (this.arrayDevoluciones[id] !== undefined) {
            this.arrayDevoluciones[id].cantidad++;
        } else {
            this.arrayDevoluciones[id] = {
                cantidad: 1,
                id: this.productos[id].id,
                name: this.productos[id].nombre,
                precio: this.productos[id].precio,
            }
        }
        this.calcularBase();
    },
    nuevaVentaAdd(id) {
        if (this.arrayNuevaVenta[id] !== undefined) {
            this.arrayNuevaVenta[id].cantidad++;
        } else {
            this.arrayNuevaVenta[id] = {
                cantidad: 1,
                id: this.productos[id].id,
                name: this.productos[id].nombre,
                precio: this.productos[id].precio,
            }
        }
        this.calcularBase();
    },
    guardarCarritoEnEspera() {
        let id = `${this.fecha.getHours().toString().padStart(2, '0')}:
                                ${this.fecha.getMinutes().toString().padStart(2, '0')}:
                                ${this.fecha.getSeconds().toString().padStart(2, '0')}`;

        // Guardar el carrito actual en carritoEspera con clave única
        this.carritoEspera[id] = this.carrito;

        // Guardar en localStorage
        localStorage.setItem('carritoEspera', JSON.stringify(this.carritoEspera));

        // Limpiar carrito actual
        this.carrito = {};
        localStorage.setItem('carrito', JSON.stringify(this.carrito));
    },
    buscarPorNombre(){
        console.log('this.search:', this.search, 'Tipo:', typeof this.search);
        let clientsWithOrders = Object.values(this.productos);

        const normalize = (string) => string.trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        const normalizeNumber = (string) => string.toString().trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        if(this.search) {
            let search = normalize(this.search)

            clientsWithOrders = clientsWithOrders.filter((producto) => normalize(producto.nombre).includes(search) || normalizeNumber(producto.categoria_id).includes(search))
        }
        if(this.searchCategory){
            let searchCategory = this.searchCategory

            clientsWithOrders = clientsWithOrders.filter((producto) => normalizeNumber(producto.categoria_id).includes(searchCategory))
        }
        
        return clientsWithOrders;
    },
    dropCarrito(id) {
        if (this.carrito[id] !== undefined) {
            delete this.carrito[id];
        }
        this.calcularBase();
        localStorage.setItem('carrito', JSON.stringify(this.carrito));
    },
    dropDevolucion(id) {
        if (this.arrayDevoluciones[id] !== undefined)
            delete this.arrayDevoluciones[id];
    },
    deleteCarrito() {
        this.carrito = {};
        this.calcularBase();
        localStorage.setItem('carrito', JSON.stringify(this.carrito));
    },
    selectProd(id) {
        console.log('id de categoria de selectProd:', id, 'Tipo:', typeof id);
        
        let idCadena = id.toString();
        console.log('id de categoria de selectProd:', idCadena, 'Tipo:', typeof idCadena);
        
        this.searchCategory = idCadena;
    },
    resetProductos() {
        this.search = '';
        this.searchCategory = '';
    },
    saveCarrito() {

        this.carritoEspera.push(this.carrito);

        this.carrito = {};
        this.calcularBase();

        localStorage.setItem('carrito', JSON.stringify(this.carrito));
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
                let ivaQtyString = this.iva[this.productos[art].iva_id].qty;
                let ivaQty = parseFloat(ivaQtyString.replace(',', '.'));
               
                precioIVA += ((this.carrito[art].cantidad * this.carrito[art].precio) * (ivaQty * 0.01));
            }
        }
        
        return precioIVA;
    },
    tipoIVA() {
        let IVA = {};
        for (let art in this.carrito) {
            let cant_iva = this.iva[this.productos[art].iva_id].qty;
            let valorIvaArticulo = this.carrito[art].valorIva;


            if (!IVA[cant_iva]) {
                IVA[cant_iva] = { 'base': 0, 'cuota': 0, 'total': 0 };
            }
            IVA[cant_iva]['cuota'] += parseFloat((this.carrito[art].cantidad * this.carrito[art].precio) * (this.iva[this.productos[art].iva_id].qty * 0.01));
            IVA[cant_iva]['base'] += parseFloat((this.carrito[art].cantidad * this.carrito[art].precio));

        }
        for (let iva_id in IVA) {
            IVA[iva_id]['total'] = IVA[iva_id]['cuota'] + IVA[iva_id]['base'];
        }
        return IVA;
    },
    elementosCarrito() {
        let lineas = 0;
        for (let art in this.carrito) {
            lineas++;
        }
        return lineas;
    },
    isUsuarioValido() {
        return this.usuarios.some(usuario => usuario.nombre === this.usuario);
    },
    confirmarFormulario() {
        if (this.cajaSeleccionada && this.isUsuarioValido()) {
            // Aquí podrías agregar la lógica adicional antes de cerrar el contenedor
            console.log('Formulario enviado');
        }
    },
    crearNuevaTarjeta() {
        var saldoInicial;
    },
    updateTotalSinDesglosar() {
        this.totalSinDesglosar = this.calcularIVA() + this.totalCarrito;
    },
    obtenerTarjetasFiltradas() {
        let filtro = this.codigoBusquedaTR.toLowerCase().trim();
        // Convertir el objeto tarjetas_regalo en un array
        let tarjetasArray = Object.values(this.tarjeta_regalo);
        // Si no hay filtro, mostrar todas las tarjetas_regalo
        if (!filtro || filtro == null) {
            return tarjetasArray;
        }
        // Filtrar tarjetas_regalo por código de búsqueda
        return tarjetasArray.filter(function(tarjeta) {
            // Aquí accedemos a la propiedad 'codigo' de cada tarjeta
            return tarjeta.codigo.toLowerCase().includes(filtro);
        });
    },
    obtenerVentasFiltradas() {
        let filtro = this.referenciaVenta.toLowerCase().trim();

        let facturasFiltradas = {}; // Objeto donde almacenaremos las facturas filtradas

        // Recorremos las propiedades del objeto this.facturas
        for (let key in this.facturas) {
            if (this.facturas.hasOwnProperty(key)) {
                let factura = this.facturas[key];

                // Verificamos si hay filtro aplicado o si el nombre de la factura incluye el filtro
                if (!filtro || factura.nombre.toLowerCase().includes(filtro)) {
                    facturasFiltradas[key] = factura; // Asignamos la factura al objeto facturasFiltradas
                }
            }
        }
        console.log(facturasFiltradas);
        return facturasFiltradas;
    },
    PruebaObtenerVentasFiltradas() {
        this.clonFacturas = Object.values(this.clonFacturas);
        let referenciaVenta = this.referenciaVenta.toLowerCase();
        return this.clonFacturas.filter(factura => factura.nombre.toLowerCase().includes(referenciaVenta));
    },
    getProductName(productoId) {
        // Verifica si el productoId existe en this.productos
        if (productoId in this.productos) {
            // Obtiene el nombre del producto usando el productoId como clave
            return this.productos[productoId].nombre || '';
        } else {
            return 'producto no encontrado';
        }
    },
    FiltrarFactura(objectId) {
        let facturaFiltrada = {};
        for (let key in this.lineas_factura) {
            if (this.lineas_factura.hasOwnProperty(key)) {
                let lineaFactura = this.lineas_factura[key];
                if (lineaFactura.object_id === objectId) {
                    facturaFiltrada[key] = lineaFactura;
                }
            }
        }
        return facturaFiltrada;
    },
    imprimirDiv(divAImprimir){
        let contentDiv = document.getElementById(divAImprimir).outerHTML;
        let printWindow = window.open('', '', 'height: 500px; width: 700px');
        printWindow.document.write(contentDiv);
        printWindow.document.close();
        printWindow.print();
    }
}" x-init="productosShow = Object.values(productos);
    calcularBase();
    totalSinDesglosar = calcularIVA() + totalCarrito;
    setInterval(() => fecha = new Date(), 1000);
    $watch('carrito', () => updateTotalSinDesglosar());
    $watch('totalCarrito', () => updateTotalSinDesglosar());
    console.log('facturas:', this.facturas, 'Tipo:', typeof this.facturas);
    console.log('productos:', productos);
    console.log('categorias:', categorias);
    console.log('carrito:', carrito);">
    {{-- columna izquierda --}}
    <div class="bg-borgoña-predeterminado text-white w-1/12 flex flex-col items-center h-full justify-between">
        <button class="my-2 text-center cursor-pointer"
            @click=" showTpv = true; showTR = false; showArqueo = false; showVentas = false;" aria-expanded="false" aria-controls="tpvSection">
            
            <i class="fa-solid fa-cash-register fa-3x px-4 pb-2 pt-4"></i>
            <p class="mx-2">TPV</p>
        </button>
        <button class="my-2 text-center cursor-pointer"
            @click=" showTpv = false; showTR = false; showArqueo = false; showVentas = true;" aria-expanded="false" aria-controls="tpvSection">
            <i class="fa-solid fa-file-invoice fa-3x px-4 pb-2 pt-4"></i>
            <p class="mx-2">Ventas</p>
        </button>
        <button class="my-2 text-center cursor-pointer"
            @click=" showTpv = false; showTR = true; showArqueo = false; showVentas = false;">
            <i class="fa-solid fa-hand-holding-heart fa-3x px-4 pb-2 pt-4"></i>
            <p class="mx-2">Sala</p>
        </button>
        <button class="my-2 text-center cursor-pointer"
            @click=" showTpv = false; showTR = false; showArqueo = true; showVentas = false;">
            <i class="fa-solid fa-folder-open fa-3x px-4 pb-2 pt-4"></i>
            <p class="mx-2">Arqueo</p>
        </button>
        <button class="my-2 text-center cursor-pointer">
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
                </form>
                <i class="fa-solid fa-sign-out fa-3x px-4 py-2 cursor-pointer" onclick="document.getElementById('logout-form').submit();"></i>
                <p class="mx-2">Salir</p>
        </button>
    </div>

    {{-- columna central --}}
    <div class="flex-1 flex flex-col w-8/12">
        {{-- navegador --}}
        <div class="bg-borgoña-claro text-white p-4 h-20 flex items-center" x-data="{ init() { this.$nextTick(() => this.$refs.input.focus()) } }" x-init="init">
            <i class="fa-solid fa-magnifying-glass fa-3x px-4 py-2 cursor-pointer"></i>
           {{-- <i class="fa-solid fa-barcode fa-3x px-4 py-2 cursor-pointer"></i> --}}
            <input x-ref="inputCB" id="navegador" name="navegador" type="text" x-model="search"
                class="rounded-full px-4 py-2 w-full bg-white text-black border border-gray-300 focus:outline-none focus:border-blue-500">
        </div>
        {{-- tpv --}}
        <div x-show="showTpv" class="flex overflow-x-auto bg-skin-primary">
            <div class="flex flex-col w-full overflow-x-auto bg-skin-primary">
                <!-- Categorías -->
                <div class="flex overflow-x-auto bg-skin-primary">
                    <div class="flex-none w-24 h-24 bg-gray-300 p-4 m-3 cursor-pointer" @click="resetProductos()">
                        <p class="text-center mt-2">Borrar Filtros</p>
                    </div>
                    <template x-for="categoria in Object.values(categorias)">
                        <div class="flex-none w-24 h-24 bg-gray-300 p-4 m-3 cursor-pointer" @click="selectProd(categoria.id)">
                            <p class="text-center mt-2" x-text="categoria.nombre"></p>
                        </div>
                    </template>
                </div>
                <!-- Productos -->
                <div class="flex-1 bg-borgoña-predeterminado overflow-y-auto h-screen flex flex-wrap justify-start">
                    <template x-for="producto in buscarPorNombre()" :key="producto.id">
                        <div class="my-3 bg-white w-36 h-36 p-2 mx-4 flex flex-col justify-center items-center cursor-pointer" @click="carritoAdd(producto.id)">
                            <img :src="producto.imagen_url" alt="Foto" class="flex-grow flex-shrink object-cover rounded-md">
                            <div class="flex justify-center items-center mt-2">
                                <p class="h-8 overflow-hidden" x-text="producto.nombre" title="producto.nombre"></p>
                                <p class="text-xs ml-2 mb-1" x-text="(parseFloat(producto.precio) || 0).toFixed(2) + '€'"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>


        {{-- tarjeta regalo --}}
        <div x-show="showTR" class="flex overflow-x-auto">
            <div class="flex flex-col w-full overflow-x-auto">
                <div class="flex items-center justify-between m-2">
                    <input x-model="codigoBusquedaTR" id="buscadorTR" name="buscadorTR" type="text"
                        placeholder="Buscar por nº mesa" @input="tarjeta_regalo = obtenerTarjetasFiltradas()"
                        class="border border-gray-400 rounded-md px-2 py-1 mr-2 w-1/4 ">
                </div>

                {{-- tabla tarjetas/vales --}}
                <div class="flex overflow-x-auto">
                    <table class="w-full rounded-lg">
                        <thead>
                            <tr class="text-white text-lg border-b-2 border-black bg-skin-primary">
                                <th class="px-4 py-2" colspan="1">ID</th>
                                <th class="px-4 py-2" colspan="1">Codigo</th>
                                <th class="px-4 py-2" colspan="1">Cantidad Inicial</th>
                                <th class="px-4 py-2" colspan="1">Saldo Actual</th>
                                <th class="px-4 py-2" colspan="1">Estado</th>
                                <th class="px-4 py-2" colspan="1">Fecha de Caducidad</th>
                                <th class="px-4 py-2" colspan="1">Deshabilitar</th>
                            </tr>
                        </thead>
                        <tbody x-show="!showVales">
                            <template x-for="tarjeta in tarjeta_regalo">
                                <template x-if="tarjeta.tipo == 'tarjeta'">
                                    <tr class="border-b-2 border-black">
                                        <td class="px-4 py-2 text-center" x-text="tarjeta.id"></td>
                                        <td class="px-4 py-2 text-center" x-text="tarjeta.codigo"></td>
                                        <td class="px-4 py-2 text-center" x-text="tarjeta.saldo_inicial"></td>
                                        <td class="px-4 py-2 text-center" x-text="tarjeta.saldo"></td>
                                        <td class="px-4 py-2 text-center"
                                            @click=" idElemento = tarjeta.id; estado = Number(!tarjeta.estado); $wire.editarTarjetaRegalo(idElemento, estado).then(() => { tarjeta.estado = estado; });">
                                            <span class="inline-block h-4 w-4 rounded-full"
                                                :class="tarjeta.estado == 1 ? 'bg-green-500' : 'bg-red-500'"></span>
                                        </td>
                                        <td class="px-4 py-2 text-center"
                                            x-text="new Date(tarjeta.fecha_caducidad).toLocaleDateString('es-ES', {day: '2-digit', month: '2-digit', year: 'numeric'})">
                                        </td>
                                        <td class="px-4 py-2 text-center"
                                            @click=" idElemento = tarjeta.id; estado = Number(!tarjeta.estado); $wire.editarTarjetaRegalo(idElemento, estado).then(() => { tarjeta.estado = estado; });">
                                            <i
                                                class="fa-regular fa-trash fa-2x px-4 pb-2 pt-4 text-red-600 cursor-pointer"></i>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                        <tbody x-show="showVales">
                            <template x-for="tarjeta in tarjeta_regalo">
                                <template x-if="tarjeta.tipo == 'vale'">
                                    <tr class="border-b-2 border-black">
                                        <td class="px-4 py-2 text-center" x-text="tarjeta.id"></td>
                                        <td class="px-4 py-2 text-center" x-text="tarjeta.codigo"></td>
                                        <td class="px-4 py-2 text-center" x-text="tarjeta.saldo_inicial"></td>
                                        <td class="px-4 py-2 text-center" x-text="tarjeta.saldo"></td>
                                        <td class="px-4 py-2 text-center"
                                            @click=" idElemento = tarjeta.id; estado = Number(!tarjeta.estado); $wire.editarTarjetaRegalo(idElemento, estado).then(() => { tarjeta.estado = estado; });">
                                            <span class="inline-block h-4 w-4 rounded-full"
                                                :class="tarjeta.estado == 1 ? 'bg-green-500' : 'bg-red-500'"></span>
                                        </td>
                                        <td class="px-4 py-2 text-center"
                                            x-text="new Date(tarjeta.fecha_caducidad).toLocaleDateString('es-ES', {day: '2-digit', month: '2-digit', year: 'numeric'})">
                                        </td>
                                        <td class="px-4 py-2 text-center"
                                            @click=" idElemento = tarjeta.id; estado = Number(!tarjeta.estado); $wire.editarTarjetaRegalo(idElemento, estado).then(() => { tarjeta.estado = estado; });">
                                            <i
                                                class="fa-regular fa-trash fa-2x px-4 pb-2 pt-4 text-red-600 cursor-pointer"></i>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Pestaña creacion tarjeta_regalo --}}
                <div x-show="showCrearTarjeta"
                    class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex justify-center items-center">
                    <div class="bg-white p-8 rounded-lg flex flex-col" style="height: 300px; width: 500px">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-4">
                                <span class="uppercase text-xl font-bold">Tarjetas</span>
                                <div class="relative inline-block w-14 align-middle select-none transition duration-200 ease-in mr-2"
                                    style="margin-right: 11px">
                                    <label class="switch">
                                        <input type="checkbox" x-model="showVales">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <span class="uppercase text-xl font-bold">Vales</span>
                            </div>
                            <div x-show="!showVales" class="uppercase mb-4 text-2xl font-bold">
                                tarjetas regalo
                            </div>
                            <div x-show="showVales" class="uppercase mb-4 text-2xl font-bold">
                                vales descuento
                            </div>
                        </div>


                        {{-- valores CUALQUIER --}}
                        <div
                            class="flex items-center justify-between">
                            {{-- creacion vales valores cualquiera --}}
                            <div x-show="showVales" class="flex flex-row justify-between items-center p-4 w-full">
                                <div class="flex flex-col mr-4">
                                    <label for="codigo" class="block mr-2">Código:</label>
                                    <input type="text" id="codigo" name="codigo" class="input"
                                        placeholder="Ingrese el código" />

                                    <label for="saldo" class="block mr-2">Saldo inicial:</label>
                                    <input type="number" id="saldo" name="saldo" class="input"
                                        placeholder="Ingrese el saldo inicial" />

                                    <label for="fechaCaducidad" class="block mr-2">Fecha de caducidad:</label>
                                    <input type="date" id="fechaCaducidad" name="fechaCaducidad" class="input"
                                        x-ref="fechaCaducidad" value="{{ date('Y-m-d', strtotime('+1 year')) }}" />

                                </div>

                                <div class="flex flex-col items-center justify-center">
                                    <button class="boton boton-success text-3xl py-3 px-5 mb-7 w-full"
                                        @click="$wire.crearTarjetaRegalo(codigo, saldo.value, $refs.fechaCaducidad.value, 'vale'); showCrearTarjeta = false;">CREAR</button>
                                    <button class="boton boton-danger text-3xl py-3 px-5 w-full"
                                        @click="showCrearTarjeta = false">CANCELAR</button>
                                </div>
                            </div>
                            {{-- creacion tarjetas valores cualquiera --}}
                            <div x-show="!showVales" class="flex flex-row justify-between items-center p-4 w-full">
                                <div class="flex flex-col mr-4">
                                    <label for="codigo" class="block mr-2">Código:</label>
                                    <input type="text" id="codigo" name="codigo" class="input"
                                        placeholder="Ingrese el código" />

                                    <label for="saldo" class="block mr-2">Saldo inicial:</label>
                                    <input type="number" id="saldo" name="saldo" class="input"
                                        placeholder="Ingrese el saldo inicial" />

                                    <label for="fechaCaducidad" class="block mr-2">Fecha de caducidad:</label>
                                    <input type="date" id="fechaCaducidad" name="fechaCaducidad" class="input"
                                        x-ref="fechaCaducidad" value="{{ date('Y-m-d', strtotime('+1 year')) }}" />

                                </div>

                                <div class="flex flex-col items-center justify-center">
                                    <button class="boton boton-success text-3xl py-3 px-5 mb-7 w-full"
                                        @click="$wire.crearTarjetaRegalo(codigo, saldo.value, $refs.fechaCaducidad.value, 'tarjeta'); showCrearTarjeta = false;">CREAR</button>
                                    <button class="boton boton-danger text-3xl py-3 px-5 w-full"
                                        @click="showCrearTarjeta = false">CANCELAR</button>
                                </div>
                            </div>
                            </div>
                        {{-- valores FIJOS --}}
                        <div
                            class="flex items-center justify-between">
                            {{-- creacion vales valores fijos --}}
                            <div x-show="showVales" class="flex flex-row justify-between items-center p-4 w-full">
                                <div class="flex flex-col mr-4">
                                    <label for="codigo" class="block mr-2">Código:</label>
                                    <input type="text" id="codigo" name="codigo" class="input"
                                        placeholder="Ingrese el código" />

                                    <label for="saldo" class="block mr-2">Saldo inicial:</label>
                                    <input type="number" id="saldo" name="saldo" class="input"
                                        placeholder="Ingrese el saldo inicial" />

                                    <label for="fechaCaducidad" class="block mr-2">Fecha de caducidad:</label>
                                    <input type="date" id="fechaCaducidad" name="fechaCaducidad" class="input"
                                        x-ref="fechaCaducidad" value="{{ date('Y-m-d', strtotime('+1 year')) }}" />

                                </div>

                                <div class="flex flex-col items-center justify-center">
                                    <button class="boton boton-success text-3xl py-3 px-5 mb-7 w-full"
                                        @click="$wire.crearTarjetaRegalo(codigo.value, saldo.value, $refs.fechaCaducidad.value, 'vale'); showCrearTarjeta = false;">CREAR</button>
                                    <button class="boton boton-danger text-3xl py-3 px-5 w-full"
                                        @click="showCrearTarjeta = false">CANCELAR</button>
                                </div>
                            </div>
                            {{-- creacion tarjetas valores fijos --}}
                            <div x-show="!showVales" class="flex flex-row justify-between items-center p-4 w-full">
                                <div class="flex flex-col mr-4">
                                    <label for="codigo" class="block mr-2">Código:</label>
                                    <input type="text" id="codigo" name="codigo" class="input"
                                        placeholder="Ingrese el código" />

                                    <label for="saldo" class="block mr-2">Saldo inicial:</label>
                                    <select id="saldo" name="saldo" class="input">
                                        <template x-for="(item, index) in valores_fijos_TR" :key="index">
                                            <option x-bind:value="item.valor" x-text="item.valor"></option>
                                        </template>
                                    </select>

                                    <label for="fechaCaducidad" class="block mr-2">Fecha de caducidad:</label>
                                    <input type="date" id="fechaCaducidad" name="fechaCaducidad" class="input"
                                        x-ref="fechaCaducidad" value="{{ date('Y-m-d', strtotime('+1 year')) }}" />

                                </div>
                                <div class="flex flex-col items-center justify-center">
                                    <button class="boton boton-success text-3xl py-3 px-5 mb-7 w-full"
                                        @click="$wire.crearTarjetaRegalo(codigo, saldo.value, $refs.fechaCaducidad.value, 'tarjeta'); showCrearTarjeta = false;">CREAR</button>
                                    <button class="boton boton-danger text-3xl py-3 px-5 w-full"
                                        @click="showCrearTarjeta = false">CANCELAR</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- arqueo --}}
        <div x-show="showArqueo" x-data="{ saldoTotal: 0 }" class="flex overflow-x-auto" x-init="">
            <div class="flex flex-col w-full overflow-x-auto">
                <div class="flex items-center justify-between m-2">
                    <input x-model="codigoBusquedaArqueo" id="buscadorArqueo" name="buscadorArqueo" type="text"
                        placeholder="Buscar por fecha" @input="obtenerTarjetasFiltradas;"
                        class="border border-gray-400 rounded-md px-2 py-1 mr-2 w-1/4 ">
                    
                </div>
                <table class="w-full">
                    <thead class="bg-skin-primary">
                        <tr class="text-white text-lg border-b-2 border-black">
                            <th class="px-4 py-2" colspan="1">Fecha</th>
                            <th class="px-4 py-2" colspan="1">Saldo Inicial</th>
                            <th class="px-4 py-2" colspan="1">Pagos Tarjeta</th>
                            <th class="px-4 py-2" colspan="1">Pagos Efectivo</th>
                            <th class="px-4 py-2" colspan="1">Cantidad Total</th>
                            <th class="px-4 py-2" colspan="1">Cantidad Final</th>
                            <th class="px-4 py-2" colspan="1">Comprobante</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template
                            x-for="arqueo in Object.values(arqueos).sort((arqueo1, arqueo2) => arqueo1.fecha > arqueo2.fecha ).reverse()">

                            <template x-if="arqueo.caja_id == cajaSeleccionada">

                                <tr class="border-b-2 border-black">
                                    <td class="px-4 py-2 text-center"
                                        x-text="arqueo.fecha.split('-').reverse().join('-')">
                                    </td>
                                    <td class="px-4 py-2 text-center" x-text="arqueo.saldo_inicial + '€'"></td>
                                    <td class="px-4 py-2 text-center" x-text="arqueo.saldo_tarjeta + '€'"></td>
                                    <td class="px-4 py-2 text-center" x-text="arqueo.saldo_efectivo + '€'"></td>
                                    <td class="px-4 py-2 text-center" x-text="arqueo.saldo_total + '€'"></td>
                                    <td class="px-4 py-2 text-center" x-text="arqueo.saldo_final + '€'"></td>
                                    <td class="px-4 py-2 text-center">
                                        <button class="boton"
                                            @click="showComprobanteArqueo = true; saldoTotal = arqueo.saldo_total;">
                                            <i class="fa-solid fa-hand-holding-dollar fa-2x px-4 pb-2 pt-4"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
            {{-- Cierre de caja --}}
            <div x-show="showComprobanteArqueo && conf.tpv_habilitar_arqueos == 1" x-data="{
                showArqueoCierre: true,
                billetesCierre: { '500': 0, '200': 0, '100': 0, '50': 0, '20': 0, '10': 0, '5': 0, '2': 0, '1': 0, '05': 0, '02': 0, '01': 0, '005': 0, '002': 0, '001': 0 },
                sumaBilletes: 0,
                calcularSumaBilletes() {
                    let valoresBilletes = {
                        '500': 500,
                        '200': 200,
                        '100': 100,
                        '50': 50,
                        '20': 20,
                        '10': 10,
                        '5': 5,
                        '2': 2,
                        '1': 1,
                        '05': 0.5,
                        '02': 0.2,
                        '01': 0.1,
                        '005': 0.05,
                        '002': 0.02,
                        '001': 0.01
                    };
                    this.sumaBilletes = Object.entries(this.billetesCierre)
                        .reduce((acc, [billete, cantidad]) => acc + (valoresBilletes[billete] * cantidad), 0);
                    this.saldoInicialSiguiente = this.saldoEsperado;
                },
                enviarDatosArqueo(usuario, cajaSeleccionada, billetesCierre, saldoInicialSiguiente, sumaBilletes) {
                    $wire.crearArqueoCierre(usuario, cajaSeleccionada, billetesCierre, saldoInicialSiguiente, sumaBilletes);
                    showComprobanteArqueo = false;
                },
                incrementar(inputKey, increment) {
                    this.billetesCierre[inputKey] += increment;
                    this.calcularSumaBilletes();
                },
                decrementar(inputKey, decrement) {
                    if (this.billetesCierre[inputKey] >= decrement) {
                        this.billetesCierre[inputKey] -= decrement;
                        this.calcularSumaBilletes();
                    }
                }
            }"
                class="flex gap-4 fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 justify-center items-center transition-all duration-500"
                x-init="calcularSumaBilletes();" @input="calcularSumaBilletes()">
                {{-- Ventana para conteo billetes cierre --}}
                <div x-show="showArqueoCierre" class=" bg-white p-4 rounded-lg flex flex-col">

                    <div class="flex">
                        <div class="w-1/3 m-2">
                            <span class="block" x-text="'Usuario: ' + usuario"></span>
                            <span class="block" x-text="'Caja: ' + cajaSeleccionada"></span>
                            <span class="block"
                                x-text="'Fecha: ' + fecha.toLocaleDateString('es-ES', {day: '2-digit', month: '2-digit', year: 'numeric'})">
                            </span>
                            <div class="w-1/3 m-2">
                                <label for="saldoEsperado" class="inline-block whitespace-nowrap">Saldo
                                    Esperado:</label>
                                <span x-text="saldoEsperado + '€'"></span>
                            </div>
                            <div class="w-1/3 m-2">
                                <label for="sumaBilletes" class="inline-block whitespace-nowrap">Saldo Total:</label>
                                <span x-text="sumaBilletes.toFixed(2) + '€'"></span>
                            </div>
                            <div class="w-1/3 m-2">
                                <label for="saldoInicialSiguiente" class="inline-block whitespace-nowrap">Saldo
                                    Inicial día siguiente:</label>
                                <span x-text="saldoInicialSiguiente + '€'"></span>
                            </div>
                            <div class="w-1/3 m-2">
                                <span class="inline-block whitespace-nowrap text-red-600"
                                    x-show="saldoEsperado !== sumaBilletes"
                                    x-text="'Descuadre: ' + (saldoEsperado - sumaBilletes).toFixed(2) + '€'"></span>
                            </div>
                            {{-- <div class="w-2/3 m-1" x-data="{ justificacion: '' }">
                                <textarea x-show="sumaBilletes !== saldoEsperado" class="w-full mt-2" rows="3" id="justificacion"
                                    name="justificacion" placeholder="Justificación descuadre" x-model="justificacion">
                                </textarea>
                            </div> --}}
                        </div>
                        {{-- billetes --}}
                        <div class="w-1/3 mr-6">
                            <template x-for="(cantidad, denominacion) in billetesCierre" :key="denominacion">
                                <template x-if="['500', '200', '100', '50', '20', '10', '5'].includes(denominacion)">
                                    <div class="mx-5 my-2 flex flex-col items-center">
                                        <label x-text="'Billete/s de ' + denominacion + '€:'"></label>
                                        <div class="flex items-center">
                                            <button @click="decrementar(denominacion, 5)"
                                                class="mx-1 boton-arqueo">-5</button>
                                            <button @click="decrementar(denominacion, 1)"
                                                class="mx-1 boton-arqueo">-1</button>
                                            <input x-model="billetesCierre[denominacion]" class="w-full"
                                                type="number" :name="'billetesCierre' + denominacion" min="0"
                                                value="0">
                                            <button @click="incrementar(denominacion, 1)"
                                                class="mx-1 boton-arqueo">+1</button>
                                            <button @click="incrementar(denominacion, 5)"
                                                class="mx-1 boton-arqueo">+5</button>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </div>
                        {{-- monedas --}}
                        <div class="w-1/3">
                            <template x-for="(cantidad, denominacion) in billetesCierre" :key="denominacion">
                                <template
                                    x-if="['2', '1', '05', '02', '01', '005', '002', '001'].includes(denominacion)">
                                    <div class="mx-5 my-2 flex flex-col items-center">
                                        <label x-text="'Moneda/s de ' + denominacion + '€:'"></label>
                                        <div class="flex items-center">
                                            <button @click="decrementar(denominacion, 5)"
                                                class="mx-1 boton-arqueo">-5</button>
                                            <button @click="decrementar(denominacion, 1)"
                                                class="mx-1 boton-arqueo">-1</button>
                                            <input x-model="billetesCierre[denominacion]" class="w-full"
                                                type="number" :name="'billetesCierre' + denominacion" min="0"
                                                value="0">
                                            <button @click="incrementar(denominacion, 1)"
                                                class="mx-1 boton-arqueo">+1</button>
                                            <button @click="incrementar(denominacion, 5)"
                                                class="mx-1 boton-arqueo">+5</button>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </div>
                    <div class="">
                        <button class="boton boton-danger text-2xl"
                            @click="showComprobanteArqueo = false">CANCELAR</button>

                        <button
                            class="boton boton-success ml-4 text-2xl"
                            @click=" VentanaRetiradaDinero = true; showArqueoCierre = false; "
                            :disabled="dineroRetirado > sumaBilletes && sumaBilletes == 0">CREAR</button>

                        <button
                            class="boton boton-success ml-4 text-2xl"
                            @click=" showArqueoCierre = false; enviarDatosArqueo(usuario, cajaSeleccionada, billetesCierre, saldoInicialSiguiente, sumaBilletes)"
                            :disabled="dineroRetirado > sumaBilletes && sumaBilletes == 0">CREAR</button>
                    </div>
                </div>
                <div x-data="{ mostrarTextarea: false, justificacion: '' }">
                    <div x-show="sumaBilletes !== saldoEsperado && !VentanaRetiradaDinero" class="contenedor_ticket"
                        @click="mostrarTextarea = true; $nextTick(() => $refs.justificacion.focus())">
                        <div class="ticket">
                            <div class="ticket_content">
                                <div class="ticket_text" x-show="!mostrarTextarea"
                                    x-text="justificacion ? '' : 'Justificante descuadre'"></div>
                                <textarea x-show="mostrarTextarea" class="w-full h-full outline-none resize-none" id="justificacion"
                                    name="justificacion" x-ref="justificacion" x-model="justificacion" style="border: none; outline: none;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Ventana de confirmacion de retirada de dinero --}}
                <div x-show="VentanaRetiradaDinero" x-data="{ retirar: false }"
                    class="bg-white p-8 rounded-lg flex flex-col">
                    <div x-show="!retirar">
                        <i class="fa-solid fa-chevron-left fa-2x px-4 pb-2 pt-4 mb-3"
                            @click=" showArqueoCierre = true; VentanaRetiradaDinero = false"></i>
                        <label class="inline-block whitespace-nowrap">¿Deseas retirar dinero?</label>
                        <button @click=" retirar = true " class="mr-4 boton boton-success">Sí</button>
                        <button
                            @click=" VentanaRetiradaDinero = true; enviarDatosArqueo(usuario, cajaSeleccionada, billetesCierre, saldoInicialSiguiente, sumaBilletes) "
                            class="boton boton-danger">No</button>
                    </div>
                    <div x-show="retirar">
                        <div>
                            <div class="flex">
                                <div class="w-1/2">
                                    <template x-for="(cantidad, denominacion) in billetesCierre"
                                        :key="denominacion">
                                        <template
                                            x-if="['500', '200', '100', '50', '20', '10', '5'].includes(denominacion)">
                                            <div class="mx-5 my-2 flex flex-col items-center">
                                                <label x-text="'Billete/s de ' + denominacion + '€:'"></label>
                                                <div class="flex items-center">
                                                    <button @click="decrementar(denominacion, 5)"
                                                        class="mx-1 boton-arqueo">-5</button>
                                                    <button @click="decrementar(denominacion, 1)"
                                                        class="mx-1 boton-arqueo">-1</button>
                                                    <input x-model="billetesCierre[denominacion]" class="w-full"
                                                        type="number" :name="'billetesCierre' + denominacion"
                                                        min="0" value="0">
                                                </div>
                                            </div>
                                        </template>
                                    </template>
                                </div>
                                <div class="w-1/2">
                                    <template x-for="(cantidad, denominacion) in billetesCierre"
                                        :key="denominacion">
                                        <template
                                            x-if="['2', '1', '05', '02', '01', '005', '002', '001'].includes(denominacion)">
                                            <div class="mx-5 my-2 flex flex-col items-center">
                                                <label x-text="'Moneda/s de ' + denominacion + '€:'"></label>
                                                <div class="flex items-center">
                                                    <button @click="decrementar(denominacion, 5)"
                                                        class="mx-1 boton-arqueo">-5</button>
                                                    <button @click="decrementar(denominacion, 1)"
                                                        class="mx-1 boton-arqueo">-1</button>
                                                    <input x-model="billetesCierre[denominacion]" class="w-full"
                                                        type="number" :name="'billetesCierre' + denominacion"
                                                        min="0" value="0">
                                                </div>
                                            </div>
                                        </template>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button
                                @click=" VentanaRetiradaDinero = true; enviarDatosArqueo(usuario, cajaSeleccionada, billetesCierre, saldoInicialSiguiente, sumaBilletes) "
                                class="boton">Completar operación</button>
                            <button @click=" retirar = false " class="boton boton-danger">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ventas --}}
        <div x-show="showVentas" class="flex overflow-x-auto" x-data="{ocultar: false }">
            <div class="flex flex-col w-full overflow-x-auto">
                <div class="flex items-center justify-between m-2">
                    <input x-model="referenciaVenta" id="buscadorVenta" name="buscadorVenta" type="text"
                        placeholder="Buscar por referencia" @input="facturas = obtenerVentasFiltradas()"
                        class="border border-gray-400 rounded-md px-2 py-1 mr-2 w-1/4 ">
                </div>
                <table class="w-full">
                    <thead class="bg-skin-primary uppercase">
                        <tr class="text-white text-lg border-b-2 border-black">
                            <th class="px-2 py-2 text-base" colspan="1">referencia</th>
                            <th class="px-2 py-2 text-base" colspan="1">tercero</th>
                            <th class="px-2 py-2 text-base" colspan="1">forma de pago</th>
                            <th class="px-2 py-2 text-base" colspan="1">fecha emisión</th>
                            <th class="px-2 py-2 text-base" colspan="1">base imponible</th>
                            <th class="px-2 py-2 text-base" colspan="1">total iva</th>
                            <th class="px-2 py-2 text-base" colspan="1">total</th>
                            <th class="px-2 py-2 text-base" colspan="1">imprimir factura</th>
                            <th  class="px-2 py-2 text-base"
                                colspan="1">
                                devolución
                            </th>
                            <th class="px-2 py-2 text-base" colspan="1">
                                cambio
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="factura in facturas">
                            <tr class="border-b-2 border-black">
                                <td class="px-2 py-2 text-center text-sm" x-text="factura.nombre "></td>
                                <td class="px-2 py-2 text-center text-sm" x-text="terceros[factura.tercero_id].nombre">
                                </td>
                                <td class="px-2 py-2 text-center text-sm" x-text="pagos[factura.forma_pago_id].nombre">
                                </td>
                                <td class="px-2 py-2 text-center text-sm"
                                    x-text="factura.fecha.split('-').reverse().join('/')"></td>
                                <td class="px-2 py-2 text-center text-sm" x-text="factura.base_imp.toFixed(2) + '€'">
                                </td>
                                <td class="px-2 py-2 text-center text-sm" x-text="factura.total_iva.toFixed(2) + '€'">
                                </td>
                                <td class="px-2 py-2 text-center text-sm" x-text="factura.total.toFixed(2) + '€'">
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <!-- Contenido de imprimir factura -->
                                    <button class="boton" @click="">
                                        <i class="fa-solid fa-file-invoice fa-3x px-4 pb-2 pt-2"></i>
                                    </button>
                                </td>
                                <td  class="px-2 py-2 text-center">
                                    <!-- Contenido de devolución -->
                                    <button class="boton"
                                        @click=" showOperacionVenta = true; ocultar= false; $wire.filtrarLineasFactura(factura.id); $wire.getFacturaDevolucion(factura.id); $wire.getHistorialDev(factura.id); operacionVenta= 'devolución';">
                                        <i class="fa-solid fa-tags fa-3x px-3 pb-2 pt-2"></i>
                                    </button>
                                </td>
                                <td  class="px-2 py-2 text-center">
                                    <!-- Contenido de cambio -->
                                    <button class="boton"
                                        @click=" showOperacionVenta = true; ocultar= false; $wire.filtrarLineasFactura(factura.id); $wire.getFacturaDevolucion(factura.id); $wire.getHistorialDev(factura.id); operacionVenta= 'cambio';">
                                        <i class="fa-solid fa-arrow-right-arrow-left fa-3x px-3 pb-2 pt-2"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div x-show="showOperacionVenta" x-data="{
                    lineasDevolucion: [],
                    saldoRestante: 0,
                    calcularIVADevolucion() {
                        let precioIVA = 0;
                        for (let art in arrayDevoluciones) {
                            if (arrayDevoluciones.hasOwnProperty(art)) {
                                precioIVA += ((arrayDevoluciones[art].cantidad * arrayDevoluciones[art].precio) * (this.iva[this.productos[art].iva_id].qty * 0.01));
                            }
                        }
                        return precioIVA;
                    },
                    calcularIVAFacDev() {
                        let precioIVA = 0;
                        for (let art in lineas_factura) {
                            if (lineas_factura.hasOwnProperty(art)) {
                                precioIVA += ((lineas_factura[art].cantidad * lineas_factura[art].precio) * (this.iva[this.productos[art].iva_id].qty * 0.01));
                            }
                        }
                        return precioIVA;
                    },
                    tipoIVADevolucion() {
                        let IVA = {};
                        let totalGeneral = 0;

                        for (let art in arrayDevoluciones) {
                            let cant_iva = iva[productos[art].iva_id].qty;
                            let valorIvaArticulo = arrayDevoluciones[art].valorIva;

                            if (!IVA[cant_iva]) {
                                IVA[cant_iva] = { 'base': 0, 'cuota': 0, 'total': 0 };
                            }
                            IVA[cant_iva]['cuota'] += parseFloat((arrayDevoluciones[art].cantidad * arrayDevoluciones[art].precio) * (iva[productos[art].iva_id].qty * 0.01));
                            IVA[cant_iva]['base'] += parseFloat((arrayDevoluciones[art].cantidad * arrayDevoluciones[art].precio));
                        }
                        for (let iva_id in IVA) {
                            IVA[iva_id]['total'] = IVA[iva_id]['cuota'] + IVA[iva_id]['base'];
                        }

                        for (let iva_id in IVA) {
                            totalGeneral += IVA[iva_id]['total'];
                        }

                        return { iva: IVA, totalGeneral: totalGeneral };
                    },
                    tipoIVAFacDev() {
                        let IVA = {};
                        let totalGeneral = 0;

                        for (let articulo of this.lineas_factura) {
                            let cant_iva = iva[articulo.iva_id]?.qty;

                            if (!IVA[cant_iva]) {
                                IVA[cant_iva] = { 'base': 0, 'cuota': 0, 'total': 0 };
                            }

                            let baseArticulo = parseFloat(articulo.qty * articulo.precio);
                            let cuotaArticulo = baseArticulo * (this.iva[articulo.iva_id]?.qty * 0.01);

                            IVA[cant_iva]['base'] += baseArticulo;
                            IVA[cant_iva]['cuota'] += cuotaArticulo;
                        }

                        // Calcular el total general sumando los totales de cada tipo de IVA
                        for (let iva_id in IVA) {
                            IVA[iva_id]['total'] = IVA[iva_id]['cuota'] + IVA[iva_id]['base'];
                            totalGeneral += IVA[iva_id]['total'];
                        }

                        return { iva: IVA, totalGeneral: totalGeneral };
                    },
                }"
                    class="flex gap-4 fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 justify-center items-center">
                </div>
            </div>
        </div>
    </div>
    {{-- columna derecha --}}
    <div class="bg-borgoña-predeterminado  text-white w-3/12 flex flex-col h-screen">

            <div class="text-white p-4 h-20 ml-auto flex items-center relative rounded-sm" x-data="{ open: false, cambioUsuario: false, fichaje:false, carritosEsperaOpen: false, productosCarritoEspera: false, configuracion: false, 
            usuario: ''}"
            @click.away="open = false; cambioUsuario = false; carritosEsperaOpen= false;">
            <i class="fa-regular fa-user fa-3x px-4 py-2 cursor-pointer" @click="open = !open"></i>
            <div x-show="open" class="absolute bg-white text-black p-4 rounded shadow-lg w-48 text-lg"
                style="top: calc(100% + 10px); right: 0;">
                <!-- Contenido del menú desplegable -->
                 @if(Auth::user()->isUser())
                <p class="cursor-pointer mb-2" @click="cambioUsuario = !cambioUsuario">Añadir usuario</p>
                @endif
                <p class="cursor-pointer mb-2" @click="fichaje = !fichaje">Fichar</p>
                <div x-show="fichaje" class="absolute bg-white text-black p-4 rounded shadow-lg w-48"
                    style="top: 0; right: calc(100% + 10px);">
                    @livewire('Fichar')
                </div>     
                <p class="cursor-pointer mt-3 mb-2 text-red-500"
                    @click=" open = false; cambioUsuario = false; carritosEsperaOpen= false; ">Cerrar</p>
                <div x-show="cambioUsuario" class="absolute bg-white text-black p-4 rounded shadow-lg w-48"
                    style="top: 0; right: calc(100% + 10px);">
                    <label for="usuarioNuevo">Nuevo Usuario:</label>
                    <input x-model="usuario" class="w-full" type="text" id="usuarioNuevo" name="usuarioNuevo">
                    <button type="button" class="boton mt-4"
                        @click="open = false; cambioUsuario = false;">Aceptar</button>
                </div>


                <div x-show="carritosEsperaOpen" class="absolute bg-white text-black p-4 rounded shadow-lg"
                    style="top: 0; right: calc(100% + 10px);">
                    {{-- <template x-for="carrito in carritoEspera">
                        <li>
                            <p x-text="'Carrito' + id"></p>
                        </li>
                    </template> --}}
                </div>
                <div x-show="productosCarritoEspera">
                    <table class="w-full mb-4">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 border">Nombre</th>
                                <th class="px-4 py-2 border">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="carrito in carritoEspera">
                                <tr>
                                    <td class="px-4 py-2 border" x-text="carrito.name"></td>
                                    <td class="px-4 py-2 border" x-text="carrito.cantidad"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div>
                        <p class="font-semibold">Total del carrito:</p>
                        <p x-text="totalSinDesglosar.toFixed(2) + '€'"></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-1 bg-white border-l-4 border-borgoña-predeterminado overflow-y-auto ">
            <table class="table-auto table-list">
                <thead class="bg-borgoña-predeterminado ">
                    <tr>
                        <th class="border-r border-black">CANT</th>
                        <th class="col-span-2 border-black">NOMBRE</th>
                        <th>SUB.TOTAL</th>
                        <th class="ml-4 border-black">TOTAL</th>
                        <th class="border-l border-black"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="articulo in carrito">
                        <tr class="bg-slate-200 text-black text-sm">
                            <td class="border-r border-black" x-text="articulo.cantidad"></td>
                            <td class="col-span-2 overflow-hidden" x-text="articulo.name"></td>
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
        <div class="grid grid-cols-2 gap-4 pl-3 bg-borgoña-claro  rounded-tl-xl rounded-tr-xl">
            {{-- Columna de datos --}}
            <div>
                <ul>
                    <li class="  pb-2">BASE IMPONIBLE:</li>
                    <li class=" pb-2">IVA:</li>
                    <li class=" pb-2">TOTAL:</li>
                </ul>
            </div>
            {{-- Columna de precios --}}
            <div class="mb-6">
                <ul>
                    <li class=" pb-2" x-text="totalCarrito.toFixed(2) + '€'"></li>
                    <li class=" pb-2" x-text="calcularIVA().toFixed(2) + '€'"></li>
                    <li class=" pb-2" x-text="totalSinDesglosar.toFixed(2) + '€'"></li>
                </ul>
            </div>
        </div>

        {{-- botones venta --}}
        <div class="bg-borgoña-predeterminado  text-white p-1 ">
            <div class="grid grid-cols-3 md:grid-cols-4 gap-4 md:gap-0">
                <button @click="deleteCarrito" class="m-1 flex-grow items-center boton boton-danger !p-4">
                    <i class="fa-solid fa-trash  cursor-pointer"></i>
                </button>
                <button @click="guardarCarritoEnEspera" class="m-1 flex-grow items-center boton">
                    <i class="fa-solid fa-floppy-disk  py-2 cursor-pointer"></i>
                </button>
                <button @click="showModal = true; calcularBase();"
                    class="md:col-span-2 m-1 flex-grow items-center boton boton-success">
                    <span class="hidden md:inline">VENDER</span>
                    <i class="fa-solid fa-cart-shopping inline md:hidden" @click="showModal = true"; calcularCambio();></i>
                </button>
            </div>
        </div>

    </div>

    {{-- ventana inicio caja y arqueo --}}

    <div id="{{ uniqid() }}" x-show="esVisible && !showInicioCaja && conf.tpv_habilitar_arqueos == 1"
        x-data="{
            sumaBilletes: 0,
            calcularSumaBilletes() {
                let valoresBilletes = {
                    '500': 500,
                    '200': 200,
                    '100': 100,
                    '50': 50,
                    '20': 20,
                    '10': 10,
                    '5': 5,
                    '2': 2,
                    '1': 1,
                    '05': 0.5,
                    '02': 0.2,
                    '01': 0.1,
                    '005': 0.05,
                    '002': 0.02,
                    '001': 0.01
                };
                return this.sumaBilletes = Object.entries(billetes)
                    .reduce((acc, [billete, cantidad]) => acc + (valoresBilletes[billete] * cantidad), 0);
            },
            enviarDatosYCerrar(usuario, cajaSeleccionada, billetes) {
                $wire.crearArqueoInicio(usuario, cajaSeleccionada, billetes);
                showInicioCaja = false;
                esVisible = false;
            },
            incrementar(inputKey, increment) {
                billetes[inputKey] += increment;
                this.calcularSumaBilletes();
            },
            decrementar(inputKey, decrement) {
                if (billetes[inputKey] >= decrement) {
                    billetes[inputKey] -= decrement;
                    this.calcularSumaBilletes();
                }
            },
            saldoInicialNumerico() {
                return Number(this.saldoInicial);
            }
        }"
        class="flex gap-4 fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 justify-center items-center transition-all duration-500"
        x-init="calcularSumaBilletes();
        console.log(usuario);"
        @input=" sumaBilletes = calcularSumaBilletes(); console.log(sumaBilletes, calcularSumaBilletes()) ">
        <div class="bg-white p-8 rounded-lg flex flex-col">

            <div class="flex">
                <div class="w-1/3 m-2">
                    <i class="fa-solid fa-chevron-left fa-3x px-4 pb-2 pt-4 mb-3"
                        @click=" esVisible=false; showSeleccionCaja= true; $wire.set('billetes', true);"></i>
                    <span class="block" x-text="'Usuario: ' + usuario"></span>
                    <span class="block" x-text="'Caja: ' + cajaSeleccionada"></span>
                    <span class="block"
                        x-text="'Fecha: ' + fecha.toLocaleDateString('es-ES', {day: '2-digit', month: '2-digit', year: 'numeric'})">
                    </span>
                    <div class="w-1/3 m-2">
                        <label for="saldoInicial" class="inline-block whitespace-nowrap">Saldo Inicial:</label>
                        <span id="saldoInicial" x-text="saldoInicialNumerico().toFixed(2) + '€'"></span>
                    </div>
                    <div class="w-1/3 m-2">
                        <label for="sumaBilletes" class="inline-block whitespace-nowrap">Conteo dinero:</label>
                        <span id="sumaBilletes" x-text="calcularSumaBilletes() + '€'"></span>
                    </div>
                    <div class="w-1/3 m-2">
                        <span class="inline-block whitespace-nowrap text-red-600"
                            x-show="saldoInicialNumerico() !== sumaBilletes"
                            x-text="'Descuadre: ' + (saldoInicial - sumaBilletes).toFixed(2) + '€'"></span>
                    </div>

                </div>
                {{-- billetes --}}
                <div class="w-1/3 m-2">
                    <template x-for="(cantidad, denominacion) in billetes" :key="denominacion">
                        <template x-if="['500', '200', '100', '50', '20', '10', '5'].includes(denominacion)">
                            <div class="mx-5 my-2 flex flex-col items-center">
                                <label x-text="'Billete/s de ' + denominacion + '€:'"></label>
                                <div class="flex items-center">
                                    <button @click="decrementar(denominacion, 5)"
                                        class="mx-1 boton-arqueo">-5</button>
                                    <button @click="decrementar(denominacion, 1)"
                                        class="mx-1 boton-arqueo">-1</button>
                                    <input x-model="billetes[denominacion]" class="w-full" type="number"
                                        :name="'billetes' + denominacion" min="0" value="0">
                                    <button @click="incrementar(denominacion, 1)"
                                        class="mx-1 boton-arqueo">+1</button>
                                    <button @click="incrementar(denominacion, 5)"
                                        class="mx-1 boton-arqueo">+5</button>
                                </div>
                            </div>
                        </template>
                    </template>
                </div>
                {{-- monedas --}}
                <div class="w-1/3 m-2">
                    <template x-for="(cantidad, denominacion) in billetes" :key="denominacion">
                        <template x-if="['2', '1', '05', '02', '01', '005', '002', '001'].includes(denominacion)">
                            <div class="mx-5 my-2 flex flex-col items-center">
                                <label x-text="'Billete/s de ' + denominacion + '€:'"></label>
                                <div class="flex items-center">
                                    <button @click="decrementar(denominacion, 5)"
                                        class="mx-1 boton-arqueo">-5</button>
                                    <button @click="decrementar(denominacion, 1)"
                                        class="mx-1 boton-arqueo">-1</button>
                                    <input x-model="billetes[denominacion]" class="w-full" type="number"
                                        :name="'billetes' + denominacion" min="0" value="0">
                                    <button @click="incrementar(denominacion, 1)"
                                        class="mx-1 boton-arqueo">+1</button>
                                    <button @click="incrementar(denominacion, 5)"
                                        class="mx-1 boton-arqueo">+5</button>
                                </div>
                            </div>
                        </template>
                    </template>
                </div>
            </div>
            <div class="">
                <button class="boton boton-success ml-4"
                    @click=" enviarDatosYCerrar(usuario, cajaSeleccionada, billetes); ";
                    :disabled="saldoInicialNumerico() !== sumaBilletes">Crear Arqueo de hoy</button>
            </div>
        </div>
    </div>

    {{-- Ventana emergente --}}
    <div class="flex gap-4 fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 justify-center items-center"
        x-show="showModal" x-data="{
            showNumericKeyboard: true,
            showCardOptions: false,
            showSala: false,
            cambio: '',
            dineroEntregado: '',
            valorIVA: calcularIVA(),
            //ultimaFactura: factura[factura.length - 1],
            calcularCambio(){
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
                    if (this.dineroEntregado.length === 0) {
                        this.dineroEntregado = '0';
                    }
                    this.calcularCambio();
                }
            },
            clearInput() {
                this.dineroEntregado = '0';
                this.calcularCambio();
            },
            pulsarTecla(tecla) {
                if (this.dineroEntregado === '0') {
                    this.dineroEntregado = '';
                }

                if (tecla === 'delete') {
                    this.deleteLastCharacter();
                } else if (tecla === 'cancel') {
                    this.clearInput();
                } else
                if (tecla === 'b5') {
                    this.dineroEntregado = (parseFloat(this.dineroEntregado) + 5).toString();
                } else if (tecla === 'b10') {
                    this.dineroEntregado = (parseFloat(this.dineroEntregado) + 10).toString();
                } else if (tecla === 'b20') {
                    this.dineroEntregado = (parseFloat(this.dineroEntregado) + 20).toString();
                } else if (tecla === 'b50') {
                    this.dineroEntregado = (parseFloat(this.dineroEntregado) + 50).toString();
                } else if (tecla === '.' && this.dineroEntregado.includes('.') === false) {
                    this.dineroEntregado += tecla;
                    this.calcularCambio();
                } else {
                    this.dineroEntregado += tecla;
                    this.calcularCambio();
                }
            },
            actualizarHora() {
                return {
                    time: new Date().toLocaleTimeString(),
                    actualizarHora() {
                        this.time = new Date().toLocaleTimeString();
                    }
                };
            },
        }" x-init="calcularCambio();">
        <div class="bg-white p-8 rounded-lg shadow-lg flex" style="height: 540px; width: 700px">
            <!-- Sección métodos de pago -->
            <div class="w-1/5 border-r pr-4">
                <div>
                    <div class="mb-6 space-y-2">
                        <!-- Botones para seleccionar método de pago -->
                        <div>
                            <button
                                @click="showNumericKeyboard = true; showCardOptions = false;"
                                class="relative border-b-2 border-black pb-1 text-lg hover:bg-gray-100 transition duration-300">
                                Efectivo
                            </button>
                        </div>
                        <div>
                            <button
                                @click="showNumericKeyboard = false; showCardOptions = true;"
                                class="relative border-b-2 border-black pb-1 text-lg hover:bg-gray-100 transition duration-300">
                                Tarjeta
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- Pago en Efectivo -->
            <div class="w-4/5 pl-4" x-show="showNumericKeyboard">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Columna de datos -->
                    <div>
                        <ul>
                            <li class="border-b-2 border-gray-300 pb-2">TOTAL:</li>
                            <li class="border-b-2 border-gray-300 pb-2">ENTREGADO:</li>
                            <li class="border-b-2 border-gray-300 pb-2">CAMBIO:</li>
                        </ul>
                    </div>
                    <!-- Columna de precios -->
                    <div class="mb-6">
                        <ul>
                            <li class="border-b-2 border-gray-300 pb-2" x-text="(totalSinDesglosar).toFixed(2) + '€'"></li>
                            <li class="border-b-2 border-gray-300 pb-2" x-text="dineroEntregado + '€'"></li>
                            <li class="border-b-2 border-gray-300 pb-2" x-text="cambio + '€'"></li>
                        </ul>
                    </div>
                </div>
        
                <!-- Teclado numérico -->
                <div class="grid grid-cols-6 gap-2 mb-6" style="height:280px;">
                    <button @click="pulsarTecla('7')" class="py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-300 w-full h-full">7</button>
                    <button @click="pulsarTecla('8')" class="py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-300 w-full h-full">8</button>
                    <button @click="pulsarTecla('9')" class="py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-300 w-full h-full">9</button>
                    <button @click="pulsarTecla('cancel')" class="py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-300 w-full h-full"><i class="fa-solid fa-trash"></i></button>
                    <button @click="pulsarTecla('b5')" class="py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-300 col-span-2">5.00€</button>
                    <button @click="pulsarTecla('4')" class="py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-300 w-full h-full">4</button>
                    <button @click="pulsarTecla('5')" class="py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-300 w-full h-full">5</button>
                    <button @click="pulsarTecla('6')" class="py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-300 w-full h-full">6</button>
                    <button @click="pulsarTecla('delete')" class="py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition duration-300 w-full h-full"><i class="fa-solid fa-delete-left"></i></button>
                    <button @click="pulsarTecla('b10')" class="py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-300 col-span-2">10.00€</button>
                    <button @click="pulsarTecla('1')" class="py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-300 w-full h-full">1</button>
                    <button @click="pulsarTecla('2')" class="py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-300 w-full h-full">2</button>
                    <button @click="pulsarTecla('3')" class="py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-300 w-full h-full">3</button>
                    <button @click="calcularCambio" class="py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-300 row-span-2 w-full h-full"><i class="fa-solid fa-arrow-turn-down transform rotate-90"></i></button>
                    <button @click="pulsarTecla('b20')" class="py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-300 col-span-2 w-full h-full">20.00€</button>
                    <button class="py-2 bg-gray-200 rounded-lg w-full h-full"></button>
                    <button @click="pulsarTecla('.')" class="py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-300 w-full h-full">.</button>
                    <button @click="pulsarTecla('0')" class="py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-300 w-full h-full">0</button>
                    <button @click="pulsarTecla('b50')" class="py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-300 col-span-2 w-full h-full">50.00€</button>
                </div>
                <div class="flex justify-between mt-4" style="height: 55px">
                    <button class="py-2 px-4 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-300 text-2xl" @click="showModal = false">CANCELAR</button>
                    <button class="py-2 px-4 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-300 text-2xl" @click="$wire.crearTicket(carrito, valorIVA, totalSinDesglosar, totalCarrito, usuario); deleteCarrito(); showModal = false;" :disabled="carrito.length === 0 || cambio < 0 || dineroEntregado <= 0">PAGAR</button> 
                </div>
            </div>
        
            <!-- Pago con Tarjeta -->
            <div class="w-4/5 pl-4" x-show="showCardOptions">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Columna de datos -->
                    <div>
                        <ul>
                            <li class="border-b-2 border-gray-300 pb-2">TOTAL:</li>
                        </ul>
                    </div>
                    <!-- Columna de precios -->
                    <div class="mb-6">
                        <ul>
                            <li class="border-b-2 border-gray-300 pb-2" x-text="(totalSinDesglosar).toFixed(2) + '€'"></li>
                        </ul>
                    </div>
                </div>
                <div>
                    <select id="bancos" name="bancos" class="form-select mt-1 block w-full rounded-lg border-gray-300">
                        <option selected disabled>Selecciona un banco</option>
                        <option value="banco1">Banco 1</option>
                        <option value="banco2">Banco 2</option>
                        <option value="banco3">Banco 3</option>
                    </select>
                </div>
                <div class="flex justify-between mt-16">
                    <button class="py-2 px-4 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-300 text-2xl" @click="showModal = false">CANCELAR</button>
                    <button class="py-2 px-4 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-300 text-2xl">PAGAR</button>
                </div>
            </div>
        </div>
        
        {{-- Ventana Ticket --}}
        <div class="">
            <div class="bg-slate-100 p-8 rounded-lg flex ml-4 flex-col uppercase overflow-y-auto overflow-x-hidden"
                style="width: 314px; height: 100vh;">
                {{-- Cabecera --}}
                {{-- Datos empresa --}}
                <div class="flex flex-col items-center mb-3 text-sm">
                    <img class="" src=""
                        alt="Descripción de la imagen">
                    <span ></span>
                    <span ></span>
                    <div>
                        <span ></span>
                        <span ></span>
                        (<span ></span>)
                    </div>
                    <div>
                        <span>Tlf . </span>
                        <span ></span>
                        <span>CIF/NIF: </span>
                        <span ></span>
                    </div>
                </div>
                {{-- Fecha --}}
                <div class="text-sm">
                    <span class="font-bold">FACTURA SIMPLIFICADA</span>
                    <div class="flex justify-between">
                        <div>
                            <span>NF: </span>
                            <span x-text="getNextRef"></span>
                        </div>
                        <div>
                            <span
                                x-text="fecha.toLocaleDateString('es-ES', {day: '2-digit', month: '2-digit', year: 'numeric'})"></span>
                            <span
                                x-text="fecha.toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'})"></span>
                        </div>
                    </div>
                </div>
                {{-- Datos compra --}}
                <div class="">
                    <div class="flex-1 bg-whit">
                        <table class="w-full">
                            <thead class="border-b border-black">
                                <tr>
                                    <th class="text-left px-2 h-5">und</th>
                                    <th class="col-span-2 text-left px-2 h-5">NOMBRE</th>
                                    <th class="px-2 h-5">PRECIO</th>
                                    <th class="ml-4 px-2 h-5">IMPORTE</th>
                                    <th class="px-2 h-5"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="articulo in carrito">
                                
                                    <tr class="text-black text-sm h-5">
                                        <td class="text-right mr-2 px-2 h-5" x-text="articulo.cantidad"></td>
                                        <td class="col-span-2 text-left flex overflow-hidden  h-5"
                                            x-text="articulo.name">
                                        </td>
                                        <td class="ml-4 text-right px-2 h-5"
                                            x-text="(articulo.precio).toFixed(2) + '€'">
                                        </td>
                                        <td class="text-right px-2 h-5"
                                            x-text="(articulo.precio * articulo.cantidad).toFixed(2) + '€'"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                    </div>
                </div>
                <div class=" mt-3">
                    <table class="w-full border-b-4 text-sm">
                        <thead>
                            <tr>
                                <th class=" text-right">Imp</th>
                                <th class=" text-right">Base</th>
                                <th class=" text-right">Cuota</th>
                                <th class=" text-right">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(value, index) in tipoIVA()">
                                <tr>

                                    <td class=" text-right" x-text="index + '%'"></td>
                                    <td class=" text-right" x-text="value.base.toFixed(2) + '€'"></td>
                                    <td class=" text-right" x-text="value.cuota.toFixed(2) + '€'"></td>
                                    <td class=" text-right" x-text="value.total.toFixed(2) + '€'"></td>
                                </tr>
                            </template>
                            <tr>
                                <td></td>
                                <td></td>
                                <td class="font-bold text-right border-t border-black text-base">TOTAL</td>
                                <td class="font-bold text-right border-t border-black text-base"
                                    x-text="(totalSinDesglosar).toFixed(2) + '€'"></td>
                            </tr>
                        </tbody>

                    </table>


                </div>
                {{-- footer --}}
                <div class="border-t border-black text-sm">
                    <div class="w-full text-sm">
                        <div class="flex flex-wrap justify-between items-start">
                            <div>
                                <span class="">ENTREGADO</span>
                                <span class="border-b-2 border-l-skin-primary pb-2 ml-1"
                                    x-text="'dineroEntregado' + '€'"></span>
                            </div>
                            <div>
                                <span>CAMBIO</span>
                                <span class="border-b-2 border-l-skin-primary pb-2 ml-1"
                                    x-text="'cambio' + '€'"></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="flex mb-2">
                            <span class="mr-2">Num. Lin. Factura</span>
                            <span x-text="elementosCarrito()"></span>
                        </div>
                        <div class="flex">
                            <span class="mr-2">Vendedor</span>
                            <span x-text="usuario"></span>
                        </div>
                        <div class="w-full mx-auto flex flex-col items-center justify-center">
                            <span class="my-2">**** IMPUESTOS INCLUIDOS ****</span>
                            <span class="my-2">GRACIAS POR SU COMPRA</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>