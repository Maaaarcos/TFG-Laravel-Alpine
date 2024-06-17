<div id="tpv" class=" h-screen flex" x-data="{
    showModal: false,
    showComprobanteArqueo: false,
    showInicioCaja: false,
    showSeleccionCaja: true,
    showTpv: true,
    showCS: false,
    showArqueo: false,
    showVentas: false,
    showOperacionVenta: false,
    esVisible: false,
    VentanaRetiradaDinero: false,
    showNumericKeyboard : false,
    showSala : false,
    showCardOptions : false,
    showUsuarioCarritoEspera: false,
    fecha: new Date(),
    search: '',
    searchCategory: '',
    searchVenta: '',
    searchArqueo: '',
    productos: @entangle('productos'),
    productosShow: null,
    categorias: @entangle('categorias'),
    iva: @entangle('iva'),
    provincia: @entangle('provincia'),
    factura: @entangle('factura'),
    getNextRef: @entangle('getNextRef'),
    arqueos: @entangle('arqueos'),
    usuarios: @entangle('usuarios'),
    user: @entangle('user'),
    usuario: '',
    cajas: @entangle('cajas'),
    selectCaja: @entangle('selectCaja'),
    saldoInicial: @entangle('saldoInicial'),
    facturas: @entangle('facturas'),
    clonFacturas: @entangle('facturas'),
    pagos: @entangle('pagos'),
    terceros: @entangle('terceros'),
    movimientos_arqueo: @entangle('movimientos_arqueo'),
    lineas_factura: @entangle('lineas_factura'),
    datosEmpresa: @entangle('datosEmpresa'),
    cajaSeleccionada: '',
    carrito: JSON.parse(localStorage.getItem('carrito')) || {},
    carritoEspera: JSON.parse(localStorage.getItem('carritoEspera')) || Array(12).fill({ productos: {}, vendedor: '' }),
    arrayDevoluciones: {},
    arrayNuevaVenta: {},
    totalCarrito: 0,
    saldoTotal: 0,
    totalSinDesglosar: 0,
    dineroRetirado: 0,
    saldoEsperado: @entangle('saldoEsperado') ,
    saldoInicialSiguiente: @entangle('saldo_inicialDS'),
    codigoBusqueda: '',
    codigoBusquedaTR: '',
    codigoBusquedaArqueo: '',
    referenciaVenta: '',
    operacionVenta: '',
    mensaje: '',
    metodoDePago: 'efectivo',
    contenidoDev: '', 
    nombreVendedor: '{{ $user }}',
    mesa: '',
    valorIVA: 0,
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
    carritoAdd(id) {
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
        this.calcularBase();
        localStorage.setItem('carrito', JSON.stringify(this.carrito));
    },
    productoExiste(codigo) {
        let producto_id = this.buscarPorCB(codigo);
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
    guardarCarritoEnEspera(nombreVendedor, mesa) {
        // Verificar si el índice de la mesa es válido
        let mesaIndicada = mesa - 1;
        if (mesa < 0 || mesa >= 12) {
            console.error('Índice de mesa inválido');
            return;
        }

        // Guardar el carrito actual en el índice especificado
        this.carritoEspera[mesaIndicada] = {
            productos: { ...this.carrito },
            vendedor: nombreVendedor  // Sustituye por el nombre real del vendedor
        };

        // Guardar en localStorage
        localStorage.setItem('carritoEspera', JSON.stringify(this.carritoEspera));

        // Limpiar carrito actual
        this.carrito = {};
        localStorage.setItem('carrito', JSON.stringify(this.carrito));
    },
    cargarCarrito(index) {
        // Verificar si el índice es válido
        if (index < 0 || index >= this.carritoEspera.length) {
            console.error('Índice inválido');
            return;
        }

        // Limpiar el carrito actual
        this.carrito = {};

        // Cargar los productos del carrito específico
        for (let id in this.carritoEspera[index].productos) {
            let producto = this.carritoEspera[index].productos[id];
            this.carrito[id] = {
                id: producto.id,
                name: producto.name,
                precio: producto.precio,
                cantidad: producto.cantidad
            };
        }

        // Actualizar el nombre del vendedor
        this.nombreVendedor = this.carritoEspera[index].vendedor;

        this.calcularBase();

        // Guardar en localStorage
        localStorage.setItem('carritoEspera', JSON.stringify(this.carritoEspera));
        localStorage.setItem('carrito', JSON.stringify(this.carrito)); // Guardar también el carrito actual
    },
    eliminarCarritoEspera(index) {
        // Verificar si el índice es válido
        if (index < 0 || index >= this.carritoEspera.length) {
            console.error('Índice inválido');
            return;
        }

        // Eliminar el carrito en el índice especificado
        this.carritoEspera[index] = {
            productos: {},
            vendedor: ''
        };

        // Guardar en localStorage
        localStorage.setItem('carritoEspera', JSON.stringify(this.carritoEspera));
    },
    buscarPorNombre(){
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
    buscarVenta(){
        let clientsWithOrders = Object.values(this.facturas);

        const normalize = (string) => string.trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        const normalizeNumber = (string) => string.toString().trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')

        if(this.searchVenta){
            let searchVenta = this.searchVenta

            clientsWithOrders = clientsWithOrders.filter((factura) => normalizeNumber(factura.name).includes(searchVenta))
        }
        return clientsWithOrders;
    },
    buscarArqueo(){
        let clientsWithOrders = Object.values(this.arqueos);

        const normalize = (string) => string.trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        const normalizeNumber = (string) => string.toString().trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')

        if(this.searchArqueo){
            let searchArqueo = this.searchArqueo

            clientsWithOrders = clientsWithOrders.filter((arqueo) => normalizeNumber(arqueo.fecha).includes(searchArqueo))
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
        let idCadena = id.toString();
        
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

            let ivaQtyString = this.iva[this.productos[art].iva_id].qty;
            let ivaQty = parseFloat(ivaQtyString.replace(',', '.'));

            IVA[cant_iva]['cuota'] += parseFloat((this.carrito[art].cantidad * this.carrito[art].precio) * (ivaQty * 0.01));
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
    updateValorIVA(){
        this.valorIVA = this.calcularIVA();
    },
    imprimirDiv(divAImprimir){

        let contentDiv = document.getElementById(divAImprimir);
        contentDiv.children[0].style.width = '100%';
        contentDiv.children[0].style.height = '100%';

        contentDiv = contentDiv.outerHTML;
        let ruta = window.location.host;

        let printWindow = window.open('', '', 'height: 100%; width: 100%');
        printWindow.document.write(contentDiv);
        printWindow.document.close();
        printWindow.addEventListener('load',()=> printWindow.print());
        printWindow.close();
    }
}" x-init="productosShow = Object.values(productos);
    calcularBase();
    totalSinDesglosar = calcularIVA() + totalCarrito;
    setInterval(() => fecha = new Date(), 1000);
    $watch('carrito', () => updateTotalSinDesglosar());
    $watch('carrito', () => updateValorIVA());
    $watch('totalCarrito', () => updateTotalSinDesglosar());">
    {{-- columna izquierda --}}
    <div class="bg-gris-oscuro text-white flex flex-col items-center justify-between h-screen md:w-1/6 lg:w-1/12">
        <div class="my-2 text-center cursor-pointer transition duration-300 ease-in-out transform hover:scale-110"
        @click="showTpv = true; showCS = false; showArqueo = false; showVentas = false;">
       <i class="fa-solid fa-cash-register fa-3x px-4 pb-2 pt-4 "></i>
       <p class="mx-2">TPV</p>
   </div>
   
   <div class="my-2 text-center cursor-pointer transition duration-300 ease-in-out transform hover:scale-110"
        @click="showTpv = false; showCS = false; showArqueo = false; showVentas = true;">
       <i class="fa-solid fa-file-invoice fa-3x px-4 pb-2 pt-4 "></i>
       <p class="mx-2 ">Ventas</p>
   </div>
   
   <div class="my-2 text-center cursor-pointer transition duration-300 ease-in-out transform hover:scale-110"
        @click="showTpv = false; showCS = true; showArqueo = false; showVentas = false;">
       <i class="fa-solid fa-store fa-3x px-4 pb-2 pt-4 "></i>
       <p class="mx-2 ">Sala</p>
   </div>
   
   <div class="my-2 text-center cursor-pointer transition duration-300 ease-in-out transform hover:scale-110"
        @click="showTpv = false; showCS = false; showArqueo = true; showVentas = false;">
       <i class="fa-solid fa-folder-open fa-3x px-4 pb-2 pt-4 "></i>
       <p class="mx-2 ">Arqueo</p>
   </div>
   
   <div class="my-2 text-center cursor-pointer transition duration-300 ease-in-out transform hover:scale-110">
       <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
           @csrf
       </form>
       <i class="fa-solid fa-sign-out fa-3x px-4 py-2 cursor-pointer "
          onclick="document.getElementById('logout-form').submit();"></i>
       <p class="mx-2 ">Salir</p>
   </div>
   
</div>


    {{-- columna central --}}
    <div class="flex-1 flex flex-col w-8/12">
        {{-- navegador --}}
        <div class="bg-gris-oscuro text-white p-4 h-20 flex items-center" >
            <i class="fa-solid fa-magnifying-glass fa-3x px-4 py-2 cursor-pointer"></i>
            <input x-ref="inputCB" id="navegador" name="navegador" type="text" x-model="search"
                class="rounded-full px-4 py-2 w-full bg-white text-black border border-gris-oscuro focus:outline-none focus:border-blue-500">
        </div>
        {{-- tpv --}}
        <div x-show="showTpv" class="flex overflow-x-auto bg-skin-primary">
            <div class="flex flex-col w-full overflow-x-auto bg-skin-primary">
               
                <!-- Categorías -->
                <div class="flex overflow-x-auto bg-skin-primary p-4 space-x-2">
                    <div class="flex-none w-24 h-20 bg-gray-300 flex items-center justify-center cursor-pointer rounded-lg shadow-lg hover:bg-gray-400 transition-all duration-300"
                        @click="resetProductos()">
                        <p class="text-center font-semibold text-gray-700">Borrar Filtros</p>
                    </div>
                    <template x-for="categoria in Object.values(categorias)">
                        <div class="flex-none w-24 h-20 bg-gray-300 flex items-center justify-center cursor-pointer rounded-lg shadow-lg hover:bg-gray-400 transition-all duration-300"
                            @click="selectProd(categoria.id)">
                            <p class="text-center font-semibold text-gray-700" x-text="categoria.nombre"></p>
                        </div>
                    </template>
                </div>


                <div class="flex-1 overflow-y-auto h-screen flex flex-wrap justify-start overflow-x-auto scrollbar-hide">
                    <!-- Estilos CSS para la barra de desplazamiento -->
                    <style>
                        .custom-scrollbar::-webkit-scrollbar {
                            width: 8px; /* Grosor de la barra */
                        }
                    
                        .custom-scrollbar::-webkit-scrollbar-track {
                            background-color: #f1f1f1; /* Color de fondo de la pista */
                        }
                    
                        .custom-scrollbar::-webkit-scrollbar-thumb {
                            background-color: #9e1414; /* Color del thumb */
                            border-radius: 4px; /* Radio de las esquinas del thumb */
                        }
                    </style>
                
                    <!-- Iteración de productos -->
                    <template x-for="producto in buscarPorNombre()" :key="producto.id">
                        <div class="my-3 bg-white w-36 h-36 p-2 mx-4 flex flex-col justify-center items-center cursor-pointer transition duration-300 ease-in-out transform hover:scale-105" @click="carritoAdd(producto.id)">
                            <div class="rounded-full overflow-hidden w-24 h-24 flex items-center justify-center">
                                <img :src="'{{ asset('storage/') }}/' +  producto.imagen_url" alt="Foto del producto" class="object-cover w-full h-full">
                            </div>
                
                            <div class="flex justify-center items-center mt-2">
                                <p class="h-8 overflow-hidden" x-text="producto.nombre" title="producto.nombre"></p>
                                <p class="text-xs ml-2 mb-1" x-text="(parseFloat(producto.precio) || 0).toFixed(2) + '€'"></p>
                            </div>
                        </div>
                    </template>
                </div>
                
                
            
                
            </div>
        </div>


        {{-- Carrito Espera --}}
        <div x-show="showCS" class="flex justify-center overflow-x-auto py-4">
            <div class="flex flex-col w-full">
                <div class="flex items-center justify-center m-2">
                    <div class="grid grid-cols-3 gap-x-32 gap-y-8"> <!-- Incrementamos el gap a 8 -->
                        <template x-for="(item, index) in Object.entries(carritoEspera)" :key="index">
                            <div class="relative bg-gray-200 p-6 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:bg-gray-300 transform hover:scale-105 transition-transform"                                @click="cargarCarrito(index)">
                                <button @click.stop="eliminarCarritoEspera(index)"
                                        class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 rounded">
                                    <i class="fa-solid fa-xmark fa-x px-1 py-1 cursor-pointer"></i>
                                </button>
                                <p class="text-lg font-semibold" x-text="'Mesa ' + (index + 1)"></p>
                                <p class="mt-2">Vendedor: <span x-text="item[1].vendedor"></span></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        

        {{-- arqueo --}}
        <div x-show="showArqueo" x-data="{ saldoTotal: 0 }" class="flex overflow-x-auto" x-init="">
            <div class="flex flex-col w-full overflow-x-auto">
                <div class="flex items-center justify-between m-2">
                    <input id="navegador" name="navegador" type="text" x-model="searchArqueo" placeholder="Filtrar por fecha"
                    class="rounded-full px-4 py-2 w-full bg-white text-black border border-gray-300 focus:outline-none focus:border-blue-500">
                    
                </div>
                <table class="w-full">
                    <thead class="bg-skin-primary">
                        <tr class="text-black text-lg border-b-2 border-black">
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
                            x-for="arqueo in buscarArqueo().sort((arqueo1, arqueo2) => arqueo1.fecha > arqueo2.fecha ).reverse()">
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
                                            @click="showComprobanteArqueo = true;">
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
            <div x-show="showComprobanteArqueo" x-data="{
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
                enviarDatosArqueo(user, cajaSeleccionada, billetesCierre, saldoInicialSiguiente, sumaBilletes) {
                    $wire.crearArqueoCierre(user, cajaSeleccionada, billetesCierre, saldoInicialSiguiente, sumaBilletes);
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
                
<div x-show="showArqueoCierre" class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex justify-center items-center transition-all duration-500 z-50">
    <div class="bg-white rounded-lg overflow-y-auto max-h-full w-full md:max-w-3xl">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <span class="block font-bold text-lg text-gray-900" x-text="'Usuario: ' + user"></span>
                    <span class="block font-bold text-lg text-gray-900" x-text="'Caja: ' + cajaSeleccionada"></span>
                    <span class="block text-sm text-gray-600" x-text="'Fecha: ' + fecha.toLocaleDateString('es-ES', {day: '2-digit', month: '2-digit', year: 'numeric'})"></span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="saldoEsperado" class="inline-block font-bold text-gray-900">Saldo Esperado:</label>
                    <span class="block text-lg font-semibold text-gray-900" x-text="saldoEsperado.toFixed(2) + '€'"></span>
                </div>
                <div>
                    <label for="sumaBilletes" class="inline-block font-bold text-gray-900">Saldo Total:</label>
                    <span class="block text-lg font-semibold text-gray-900" x-text="sumaBilletes.toFixed(2) + '€'"></span>
                </div>
                <div>
                    <label for="saldoInicialSiguiente" class="inline-block font-bold text-gray-900">Saldo Inicial día siguiente:</label>
                    <span class="block text-lg font-semibold text-gray-900" x-text="saldoInicialSiguiente.toFixed(2) + '€'"></span>
                </div>
                <div class="col-span-3 mt-4">
                    <span class="block text-red-600 font-bold" x-show="saldoEsperado !== sumaBilletes" x-text="'Descuadre: ' + (saldoEsperado - sumaBilletes).toFixed(2) + '€'"></span>
                </div>
                <div class="col-span-3">
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Billetes --}}
                        <template x-for="(cantidad, denominacion) in billetesCierre" :key="denominacion">
                            <template x-if="['500', '200', '100', '50', '20', '10', '5'].includes(denominacion)">
                                <div class="flex items-center justify-between bg-gray-100 rounded p-2">
                                    <label class="font-bold text-gray-900" x-text="'Billetes de ' + denominacion + '€:'"></label>
                                    <div class="flex items-center space-x-2">
                                        <button @click="decrementar(denominacion, 5)" class="boton-arqueo bg-gray-200 text-gray-700 px-2 py-1 rounded">-5</button>
                                        <button @click="decrementar(denominacion, 1)" class="boton-arqueo bg-gray-200 text-gray-700 px-2 py-1 rounded">-1</button>
                                        <input x-model="billetesCierre[denominacion]" class="w-16 text-center bg-gray-200 text-gray-700 rounded" type="number" :name="'billetesCierre' + denominacion" min="0" value="0">
                                        <button @click="incrementar(denominacion, 1)" class="boton-arqueo bg-gray-200 text-gray-700 px-2 py-1 rounded">+1</button>
                                        <button @click="incrementar(denominacion, 5)" class="boton-arqueo bg-gray-200 text-gray-700 px-2 py-1 rounded">+5</button>
                                    </div>
                                </div>
                            </template>
                        </template>
                        {{-- Monedas --}}
                        <template x-for="(cantidad, denominacion) in billetesCierre" :key="denominacion">
                            <template x-if="['2', '1', '05', '02', '01', '005', '002', '001'].includes(denominacion)">
                                <div class="flex items-center justify-between bg-gray-100 rounded p-2">
                                    <label class="font-bold text-gray-900" x-text="'Monedas de ' + denominacion + '€:'"></label>
                                    <div class="flex items-center space-x-2">
                                        <button @click="decrementar(denominacion, 5)" class="boton-arqueo bg-gray-200 text-gray-700 px-2 py-1 rounded">-5</button>
                                        <button @click="decrementar(denominacion, 1)" class="boton-arqueo bg-gray-200 text-gray-700 px-2 py-1 rounded">-1</button>
                                        <input x-model="billetesCierre[denominacion]" class="w-16 text-center bg-gray-200 text-gray-700 rounded" type="number" :name="'billetesCierre' + denominacion" min="0" value="0">
                                        <button @click="incrementar(denominacion, 1)" class="boton-arqueo bg-gray-200 text-gray-700 px-2 py-1 rounded">+1</button>
                                        <button @click="incrementar(denominacion, 5)" class="boton-arqueo bg-gray-200 text-gray-700 px-2 py-1 rounded">+5</button>
                                    </div>
                                </div>
                            </template>
                        </template>
                    </div>
                </div>
                {{-- Justificación de descuadre --}}
                <div class="col-span-3 mt-4">
                    <div x-data="{ mostrarTextarea: false, justificacion: '' }">
                        <div class="contenedor_ticket border border-gray-300 p-4 rounded-lg cursor-pointer bg-gray-100">
                            <div class="ticket_text text-gray-700" x-show="!mostrarTextarea">Añadir justificación</div>
                            <textarea x-show="mostrarTextarea" class="w-full mt-2 p-2 border border-gray-300 rounded" id="justificacion" name="justificacion" x-ref="justificacion" x-model="justificacion"></textarea>
                        </div>
                    </div>
                </div>
                {{-- Botones de acción --}}
                <div class="col-span-3 mt-4 flex justify-end space-x-4">
                    <button class="boton boton-danger px-4 py-2 text-lg bg-red-500 text-white rounded hover:bg-red-600" @click="showComprobanteArqueo = false">CANCELAR</button>
                    <button class="boton boton-success px-4 py-2 text-lg bg-green-500 text-white rounded hover:bg-green-600" @click="VentanaRetiradaDinero = true; showArqueoCierre = false;" :disabled="dineroRetirado > sumaBilletes && sumaBilletes == 0">CREAR</button>
                </div>
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
        @click=" VentanaRetiradaDinero = true; enviarDatosArqueo(user, cajaSeleccionada, billetesCierre, saldoInicialSiguiente, sumaBilletes);  $wire.cierreCajaAndUpdate(cajaSeleccionada, sumaBilletes)"
        class="boton boton-danger">No</button>
</div>
<div x-show="retirar" class="mt-4 max-h-screen overflow-y-auto bg-white p-8 rounded-lg">
    <div class="flex">
        <div class="w-1/2">
            <template x-for="(cantidad, denominacion) in billetesCierre" :key="denominacion">
                <template x-if="['500', '200', '100', '50', '20', '10', '5'].includes(denominacion)">
                    <div class="mx-5 my-2 flex flex-col items-center">
                        <label x-text="'Billete/s de ' + denominacion + '€:'"></label>
                        <div class="flex items-center">
                            <button @click="decrementar(denominacion, 5)" class="mx-1 boton-arqueo">-5</button>
                            <button @click="decrementar(denominacion, 1)" class="mx-1 boton-arqueo">-1</button>
                            <input x-model="billetesCierre[denominacion]"
                                   class="p-2 border border-gray-300 rounded w-full focus:outline-none focus:border-blue-500"
                                   type="number" :name="'billetesCierre' + denominacion" min="0" value="0">
                        </div>
                    </div>
                </template>
            </template>
        </div>
        <div class="w-1/2">
            <template x-for="(cantidad, denominacion) in billetesCierre" :key="denominacion">
                <template x-if="['2', '1', '05', '02', '01', '005', '002', '001'].includes(denominacion)">
                    <div class="mx-5 my-2 flex flex-col items-center">
                        <label x-text="'Moneda/s de ' + denominacion + '€:'"></label>
                        <div class="flex items-center">
                            <button @click="decrementar(denominacion, 5)" class="mx-1 boton-arqueo">-5</button>
                            <button @click="decrementar(denominacion, 1)" class="mx-1 boton-arqueo">-1</button>
                            <input x-model="billetesCierre[denominacion]"
                                   class="p-2 border border-gray-300 rounded w-full focus:outline-none focus:border-blue-500"
                                   type="number" :name="'billetesCierre' + denominacion" min="0" value="0">
                        </div>
                    </div>
                </template>
            </template>
        </div>
    </div>
    <div class="mt-4 flex justify-end">
        <button @click="VentanaRetiradaDinero = true; enviarDatosArqueo(user, cajaSeleccionada, billetesCierre, saldoInicialSiguiente, sumaBilletes); $wire.cierreCajaAndUpdate(cajaSeleccionada, sumaBilletes);"
                class="boton bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-md mr-2">Completar operación</button>
        <button @click="retirar = false" class="boton boton-danger bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded-md">Cancelar</button>
    </div>
</div>

</div>
            </div>
        </div>

        {{-- ventas --}}
        <div x-show="showVentas" class="flex overflow-x-auto" x-data="{ocultar: false }">
            <div class="flex flex-col w-full overflow-x-auto">
                <div class="flex items-center justify-between m-2">
                <input id="navegador" name="navegador" type="text" x-model="searchVenta" placeholder="Filtrar por referencia"
                    class="rounded-full px-4 py-2 w-full bg-white text-black border border-gray-300 focus:outline-none focus:border-blue-500">
                </div>
                <table class="w-full">
                    <thead class="bg-skin-primary uppercase">
                        <tr class="text-black text-lg border-b-2 border-black">
                            <th class="px-2 py-2 text-base" colspan="1">referencia</th>
                            <th class="px-2 py-2 text-base" colspan="1">tercero</th>
                            <th class="px-2 py-2 text-base" colspan="1">fecha</th>
                            <th class="px-2 py-2 text-base" colspan="1">forma de pago</th>
                            <th class="px-2 py-2 text-base" colspan="1">base imponible</th>
                            <th class="px-2 py-2 text-base" colspan="1">total iva</th>
                            <th class="px-2 py-2 text-base" colspan="1">total</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <template
                              x-for="factura in buscarVenta().sort((venta1, venta2) => venta1.fecha > venta2.fecha ).reverse()">
                            <tr class="border-b-2 border-black">
                                <td class="px-2 py-2 text-center text-sm" x-text="factura.name "></td>
                                <td class="px-2 py-2 text-center text-sm" x-text="terceros[factura.tercero_id].nombre">
                                </td>
                                <td class="px-2 py-2 text-center text-sm"
                                    x-text="factura.fecha.split('-').reverse().join('/')">
                                </td>
                                <td class="px-2 py-2 text-center text-sm" x-text="pagos[factura.forma_pago_id].name">
                                </td>
                                <td class="px-2 py-2 text-center text-sm" x-text="factura.base_imp + '€'">
                                </td>
                                <td class="px-2 py-2 text-center text-sm" x-text="factura.total_iva + '€'">
                                </td>
                                <td class="px-2 py-2 text-center text-sm" x-text="factura.total  + '€'">
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
    <div class="bg-gris-oscuro text-white w-3/12 flex flex-col h-screen">

            <div class="text-white p-4 h-20 ml-auto flex items-center relative rounded-sm" x-data="{ open: false, cambioUsuario: false, fichaje:false, carritosEsperaOpen: false, productosCarritoEspera: false, configuracion: false, 
            usuario: ''}"
            @click.away="open = false; cambioUsuario = false; carritosEsperaOpen= false;">
            <i class="fa-regular fa-user fa-3x px-4 py-2 cursor-pointer" @click="open = !open"></i>
            <div x-show="open" class="absolute bg-white text-black p-4 rounded shadow-lg w-48 text-lg"
                style="top: calc(100% + 10px); right: 0;">
                <!-- Contenido del menú desplegable -->
                 @if(Auth::user()->isAdmin())
                <p class="cursor-pointer mb-2" @click="cambioUsuario = !cambioUsuario">Añadir usuario</p>
                @endif
                <p class="cursor-pointer mb-2" @click="fichaje = !fichaje">Fichar</p>
                <div x-show="fichaje" class="absolute bg-white text-black p-4 rounded shadow-lg w-48"
                    style="top: 0; right: calc(100% + 10px);">
                    @livewire('Fichar')
                </div>
                @if(Auth::user()->isAdmin())
                <a href="{{ route('gestion-inventario') }}" class="boton">Administración</a>
                @endif
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
        <div class="flex-1 bg-white border-l-4 border-gris-oscuro overflow-y-auto">
            <table class="table-auto table-list bg-gris-predeterminado w-full">
                <thead>
                    <tr>
                        <th class="border-r border-black text-center">CANT</th>
                        <th class="col-span-2 border-black text-center">NOMBRE</th>
                        <th class="text-center">SUB.TOTAL</th>
                        <th class="ml-4 text-center">TOTAL</th>
                        <th class="border-l border-black"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="articulo in carrito">
                        <tr class="bg-white text-black text-sm">
                            <td class="border-r border-black text-center" x-text="articulo.cantidad"></td>
                            <td class="col-span-2 overflow-hidden text-center" x-text="articulo.name"></td>
                            <td class="ml-4 text-center" x-text="(articulo.precio).toFixed(2) + '€'"></td>
                            <td class="text-center" x-text="(articulo.precio * articulo.cantidad).toFixed(2) + '€'"></td>
                            <td class="border-l border-black text-center cursor-pointer" @click="dropCarrito(articulo.id)">
                                <i class="fa-solid fa-trash text-red-600"></i>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <div class="grid grid-cols-2 gap-4 pl-3 bg-gris-predeterminado rounded-tl-xl rounded-tr-xl">
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
        <div x-show="showUsuarioCarritoEspera" class="absolute bg-white text-black p-4 rounded shadow-lg w-72">
            <h2 class="text-lg font-bold mb-4">Guardar Carrito en Espera</h2>
            <form>
                <div class="mb-4">
                    <label for="nombreVendedor" class="block text-sm font-medium text-gray-700">Nombre del vendedor</label>
                    <input id="nombreVendedor" type="text" x-model="nombreVendedor"
                           placeholder="Nombre del vendedor"
                           class="p-2 border border-gray-300 rounded w-full focus:outline-none focus:border-blue-500"
                           readonly>
                </div>
                <div class="mb-4">
                    <label for="mesa" class="block text-sm font-medium text-gray-700">Número de mesa</label>
                    <input id="mesa" type="number" x-model="mesa" min="0" max="11"
                           placeholder="Número de mesa"
                           class="p-2 border border-gray-300 rounded w-full focus:outline-none focus:border-blue-500">
                </div>
                <div class="flex justify-end">
                    <button type="button"
                            @click="showUsuarioCarritoEspera = false;"
                            class="boton boton-danger text-sm px-4 py-2 rounded-md bg-red-500 hover:bg-red-700 text-white hover:text-white">Cancelar</button>
                    <button type="button"
                            @click="guardarCarritoEnEspera(nombreVendedor, mesa); showUsuarioCarritoEspera = false;"
                            class="ml-2 boton boton-success text-sm px-4 py-2 rounded-md bg-green-500 hover:bg-green-700 text-white hover:text-white">Guardar</button>
                </div>
            </form>
        </div>
        
        <div class="bg-gris-oscuro text-white p-1">
            <div class="grid grid-cols-3 md:grid-cols-4 gap-4 md:gap-0">
                <button @click="deleteCarrito" class="m-1 flex-grow items-center boton boton-danger !p-4">
                    <i class="fa-solid fa-trash cursor-pointer"></i>
                </button>
                <button @click="showUsuarioCarritoEspera = true;" 
                        class="m-1 flex-grow items-center boton">
                    <i class="fa-solid fa-floppy-disk py-2 cursor-pointer"></i>
                </button>
                <button @click="showModal = true; showUsuarioCarritoEspera = false;" 
                        class="md:col-span-2 m-1 flex-grow items-center boton boton-success">
                    <span class="hidden md:inline">VENDER</span>
                    <i class="fa-solid fa-cart-shopping inline md:hidden"></i>
                </button>
            </div>
        </div>

    </div>

    {{-- ventana inicio caja y arqueo --}}
    <div id="{{ uniqid() }}" x-show="showSeleccionCaja" x-data="{
            sacarDatosInicioArqueo(cajaSeleccionada) {
                $wire.comprobarArqueo(cajaSeleccionada).then(result => showInicioCaja = result);
                $wire.valorSaldoIncial(cajaSeleccionada);
                showSeleccionCaja = false;
                esVisible = true;
            }
        }"
        class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex justify-center items-center duration-500">

        <div class="bg-white p-8 rounded-lg shadow-md max-w-md mx-auto">
            <div class="mb-4">
                <label for="usuario" class="block text-sm font-medium text-gray-700">Usuario:</label>
                <span class="text-gray-900" x-text="user"></span>
            </div>
            <div class="mb-4">
                <label for="caja" class="block text-sm font-medium text-gray-700">Caja:</label>
                <select x-model="cajaSeleccionada" id="caja" name="caja" class="w-full mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="" disabled selected>Selecciona una caja</option>
                    <template x-for="caja in cajas" :key="caja.id">
                        <option x-bind:value="caja.id" x-text="caja.name"></option>
                    </template>
                </select>
            </div>
            <button type="button" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring focus:ring-blue-200"
                    :disabled="!cajaSeleccionada"
                    @click="sacarDatosInicioArqueo(cajaSeleccionada); $wire.valorSaldoIncial(cajaSeleccionada); $wire.valorBilletes(cajaSeleccionada).then(result => { billetes = result; });">
                Aceptar
            </button>
        </div>
        
    </div>
    <div id="{{ uniqid() }}" x-show="esVisible && !showInicioCaja"
     x-data="{
         sumaBilletes: 0,
         calcularSumaBilletes() {
             let valoresBilletes = {
                 '500': 500, '200': 200, '100': 100, '50': 50,
                 '20': 20, '10': 10, '5': 5, '2': 2,
                 '1': 1, '05': 0.5, '02': 0.2,
                 '01': 0.1, '005': 0.05, '002': 0.02, '001': 0.01
             };
             return this.sumaBilletes = Object.entries(billetes)
                 .reduce((acc, [billete, cantidad]) => acc + (valoresBilletes[billete] * cantidad), 0);
         },
         enviarDatosYCerrar(cajaSeleccionada, billetes) {
             $wire.crearArqueoInicio(cajaSeleccionada, billetes);
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
     class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex justify-center items-center transition-all duration-500"
     x-init="calcularSumaBilletes();"
     @input="sumaBilletes = calcularSumaBilletes();">
    <div class="bg-white p-8 rounded-lg flex flex-col w-full max-w-3xl">
        <div class="flex">
            <div class="w-1/3 m-2">
                <i class="fa-solid fa-chevron-left fa-3x px-4 pb-2 pt-4 mb-3 cursor-pointer text-gray-600"
                   @click="esVisible=false; showSeleccionCaja=true; $wire.set('billetes', true);"></i>
                <span class="block text-gray-800 font-bold" x-text="'Usuario: ' + user"></span>
                <span class="block text-gray-800 font-bold" x-text="'Caja: ' + cajaSeleccionada"></span>
                <span class="block text-gray-800 font-bold"
                      x-text="'Fecha: ' + fecha.toLocaleDateString('es-ES', {day: '2-digit', month: '2-digit', year: 'numeric'})"></span>
                <div class="mt-4">
                    <label for="saldoInicial" class="block text-gray-800 font-bold">Saldo Inicial:</label>
                    <span id="saldoInicial" x-text="saldoInicialNumerico().toFixed(2) + '€'"></span>
                </div>
                <div class="mt-4">
                    <label for="sumaBilletes" class="block text-gray-800 font-bold">Conteo dinero:</label>
                    <span id="sumaBilletes" x-text="calcularSumaBilletes() + '€'"></span>
                </div>
                <div class="mt-4">
                    <span class="block text-red-600 font-bold" x-show="saldoInicialNumerico() !== sumaBilletes"
                          x-text="'Descuadre: ' + (saldoInicial - sumaBilletes).toFixed(2) + '€'"></span>
                </div>
            </div>
            <!-- Billetes -->
            <div class="w-1/3 m-2">
                <template x-for="(cantidad, denominacion) in billetes" :key="denominacion">
                    <template x-if="['500', '200', '100', '50', '20', '10', '5'].includes(denominacion)">
                        <div class="mx-5 my-2">
                            <label x-text="'Billete/s de ' + denominacion + '€:'" class="block text-gray-800 font-bold"></label>
                            <div class="flex items-center">
                                <button @click="decrementar(denominacion, 5)" class="boton-arqueo bg-red-500 hover:bg-red-600 text-white rounded-md px-3 py-1 mr-1">-5</button>
                                <button @click="decrementar(denominacion, 1)" class="boton-arqueo bg-red-500 hover:bg-red-600 text-white rounded-md px-3 py-1 mr-1">-1</button>
                                <input x-model="billetes[denominacion]" class="w-full ml-2 mr-2 rounded border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="number" :name="'billetes' + denominacion" min="0" value="0">
                                <button @click="incrementar(denominacion, 1)" class="boton-arqueo bg-green-500 hover:bg-green-600 text-white rounded-md px-3 py-1 mr-1">+1</button>
                                <button @click="incrementar(denominacion, 5)" class="boton-arqueo bg-green-500 hover:bg-green-600 text-white rounded-md px-3 py-1 mr-1">+5</button>
                            </div>
                        </div>
                    </template>
                </template>
            </div>
            <!-- Monedas -->
            <div class="w-1/3 m-2">
                <template x-for="(cantidad, denominacion) in billetes" :key="denominacion">
                    <template x-if="['2', '1', '05', '02', '01', '005', '002', '001'].includes(denominacion)">
                        <div class="mx-5 my-2">
                            <label x-text="'Moneda/s de ' + denominacion + '€:'" class="block text-gray-800 font-bold"></label>
                            <div class="flex items-center">
                                <button @click="decrementar(denominacion, 5)" class="boton-arqueo bg-red-500 hover:bg-red-600 text-white rounded-md px-3 py-1 mr-1">-5</button>
                                <button @click="decrementar(denominacion, 1)" class="boton-arqueo bg-red-500 hover:bg-red-600 text-white rounded-md px-3 py-1 mr-1">-1</button>
                                <input x-model="billetes[denominacion]" class="w-full ml-2 mr-2 rounded border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="number" :name="'billetes' + denominacion" min="0" value="0">
                                <button @click="incrementar(denominacion, 1)" class="boton-arqueo bg-green-500 hover:bg-green-600 text-white rounded-md px-3 py-1 mr-1">+1</button>
                                <button @click="incrementar(denominacion, 5)" class="boton-arqueo bg-green-500 hover:bg-green-600 text-white rounded-md px-3 py-1 mr-1">+5</button>
                            </div>
                        </div>
                    </template>
                </template>
            </div>
        </div>
        <div class="mt-6">
            <button class="boton boton-success w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded"
                    @click="enviarDatosYCerrar(cajaSeleccionada, billetes);"
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
        <div class="bg-white p-8 rounded-lg flex" style="height: 500px; width: 700px">
            <!-- Sección métodos de pago -->
            <div class="w-1/5">
                <div>
                    <div class="mb-6 space-y-2">
                        <!-- Botones para seleccionar método de pago -->
                        <div>
                            <button @click="dineroEntregado = 0; showNumericKeyboard = true; showCardOptions = false; metodoDePago = 'efectivo';" 
                                    class="relative border-b-2 border-black hover:bg-gray-200 transition-colors duration-300">
                                Efectivo
                            </button>
                        </div>
                        <div>
                            <button @click="dineroEntregado = totalSinDesglosar; cambio = 0; showNumericKeyboard = false; showCardOptions = true; metodoDePago = 'tarjeta';" 
                                    class="relative border-b-2 border-black hover:bg-gray-200 transition-colors duration-300">
                                Tarjeta
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- Pago con Efectivo -->
            <div class="w-4/5 max-w-md mx-auto p-4 bg-white rounded-lg" x-show="showNumericKeyboard">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <!-- Columna de datos -->
                    <div>
                        <ul>
                            <li class="border-b-2 border-l-skin-primary pb-2 font-bold text-lg">TOTAL:</li>
                            <li class="border-b-2 border-l-skin-primary pb-2 font-bold text-lg">ENTREGADO:</li>
                            <li class="border-b-2 border-l-skin-primary pb-2 font-bold text-lg">CAMBIO:</li>
                        </ul>
                    </div>
                    <!-- Columna de precios -->
                    <div class="mb-6">
                        <ul>
                            <li class="border-b-2 border-l-skin-primary pb-2 font-bold text-lg" x-text="(totalSinDesglosar).toFixed(2) + '€'"></li>
                            <li class="border-b-2 border-l-skin-primary pb-2 font-bold text-lg" x-text="dineroEntregado + '€'"></li>
                            <li class="border-b-2 border-l-skin-primary pb-2 font-bold text-lg" x-text="cambio + '€'"></li>
                        </ul>
                    </div>
                </div>
        
                <!-- Teclado numérico -->
                <div class="grid grid-cols-4 gap-2 mb-6">
                    <button @click="pulsarTecla('7')" class="py-2 bg-blue-500 text-white font-bold rounded w-full h-full hover:bg-blue-600 transition-colors duration-300">7</button>
                    <button @click="pulsarTecla('8')" class="py-2 bg-blue-500 text-white font-bold rounded w-full h-full hover:bg-blue-600 transition-colors duration-300">8</button>
                    <button @click="pulsarTecla('9')" class="py-2 bg-blue-500 text-white font-bold rounded w-full h-full hover:bg-blue-600 transition-colors duration-300">9</button>
                    <button @click="pulsarTecla('cancel')" class="py-2 bg-red-500 text-white font-bold rounded w-full h-full hover:bg-red-600 transition-colors duration-300">
                        <i class="fa-solid fa-trash cursor-pointer"></i>
                    </button>
        
                    <button @click="pulsarTecla('4')" class="py-2 bg-blue-500 text-white font-bold rounded w-full h-full hover:bg-blue-600 transition-colors duration-300">4</button>
                    <button @click="pulsarTecla('5')" class="py-2 bg-blue-500 text-white font-bold rounded w-full h-full hover:bg-blue-600 transition-colors duration-300">5</button>
                    <button @click="pulsarTecla('6')" class="py-2 bg-blue-500 text-white font-bold rounded w-full h-full hover:bg-blue-600 transition-colors duration-300">6</button>
                    <button @click="pulsarTecla('delete')" class="py-2 bg-yellow-500 text-white font-bold rounded w-full h-full hover:bg-yellow-600 transition-colors duration-300">
                        <i class="fa-solid fa-delete-left cursor-pointer"></i>
                    </button>
        
                    <button @click="pulsarTecla('1')" class="py-2 bg-blue-500 text-white font-bold rounded w-full h-full hover:bg-blue-600 transition-colors duration-300">1</button>
                    <button @click="pulsarTecla('2')" class="py-2 bg-blue-500 text-white font-bold rounded w-full h-full hover:bg-blue-600 transition-colors duration-300">2</button>
                    <button @click="pulsarTecla('3')" class="py-2 bg-blue-500 text-white font-bold rounded w-full h-full hover:bg-blue-600 transition-colors duration-300">3</button>
                    <button @click="dineroEntregado = totalSinDesglosar; calcularCambio();" class="py-2 bg-green-500 text-white font-bold rounded row-span-2 w-full h-full hover:bg-green-600 transition-colors duration-300">
                        <i class="fa-solid fa-arrow-turn-down transform rotate-90"></i>
                    </button>
        
                    <button class="py-2 bg-gray-500 text-white font-bold rounded w-full h-full"></button>
                    <button @click="pulsarTecla('.')" class="py-2 bg-blue-500 text-white font-bold rounded w-full h-full hover:bg-blue-600 transition-colors duration-300">.</button>
                    <button @click="pulsarTecla('0')" class="py-2 bg-blue-500 text-white font-bold rounded w-full h-full hover:bg-blue-600 transition-colors duration-300">0</button>
                </div>
        
                <div class="flex justify-between" style="height: 55px">
                    <button class="boton boton-danger text-2xl bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600 transition-colors duration-300" @click="showModal = false">CANCELAR</button>
                    <button class="boton boton-success text-2xl bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600 transition-colors duration-300"
                            @click="$wire.crearTicket(carrito, valorIVA, totalSinDesglosar, totalCarrito, user, metodoDePago, cajaSeleccionada); imprimirDiv('contenidoAImprimir'); deleteCarrito(); showModal = false;"
                            :disabled="carrito.length === 0 || cambio < 0 || dineroEntregado <= 0">PAGAR</button>
                </div>
            </div>
        
            <!-- Pago con Tarjeta -->
            <div class="w-4/5 max-w-md mx-auto p-4 bg-white rounded-lg" x-show="showCardOptions">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <!-- Columna de datos -->
                    <div>
                        <ul>
                            <li class="border-b-2 border-l-skin-primary pb-2 font-bold text-lg">TOTAL:</li>
                        </ul>
                    </div>
                    <!-- Columna de precios -->
                    <div class="mb-6">
                        <ul>
                            <li class="border-b-2 border-l-skin-primary pb-2 font-bold text-lg" x-text="(totalSinDesglosar).toFixed(2) + '€'"></li>
                        </ul>
                    </div>
                </div>
        
                <div class="flex justify-between mt-16">
                    <button class="boton boton-danger text-2xl bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600 transition-colors duration-300" @click="showModal = false">CANCELAR</button>
                    <button class="boton boton-success text-2xl bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600 transition-colors duration-300"
                            @click="$wire.crearTicket(carrito, valorIVA, totalSinDesglosar, totalCarrito, user, metodoDePago, cajaSeleccionada); imprimirDiv('contenidoAImprimir'); deleteCarrito(); showModal = false;">PAGAR</button>
                </div>
            </div>
        </div>
        
        
        {{-- Ventana Ticket --}}
        <div id="contenidoAImprimir" class="contenidoDev">
            <div class="ticket-container bg-slate-100 p-4 rounded-lg uppercase overflow-y-auto overflow-x-hidden">
                <!-- Cabecera -->
                <!-- Datos empresa -->
                <div class="flex flex-col items-center mb-3 text-xs">
                    <span x-text="datosEmpresa.nombre"></span>
                    <span x-text="datosEmpresa.direccion"></span>
                    <div>
                        <span x-text="datosEmpresa.cod_postal"></span>
                        <span x-text="datosEmpresa.ciudad + ' '"></span>
                        (<span x-text="datosEmpresa.provincia"></span>)
                    </div>
                    <div>
                        <span>Tlf . </span>
                        <span x-text="datosEmpresa.telefono"></span>
                        <span>CIF/NIF: </span>
                        <span x-text="datosEmpresa.nif"></span>
                    </div>
                </div>
                <!-- Fecha -->
                <div class="text-xs mb-3">
                    <span class="font-bold">FACTURA SIMPLIFICADA</span>
                    <div class="flex justify-between">
                        <div>
                            <span>NF: </span>
                            <span x-text="getNextRef"></span>
                        </div>
                        <div>
                            <span x-text="fecha.toLocaleDateString('es-ES', {day: '2-digit', month: '2-digit', year: 'numeric'})"></span>
                            <span x-text="fecha.toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'})"></span>
                        </div>
                    </div>
                </div>
                <!-- Datos compra -->
                <div>
                    <table class="w-full text-xs">
                        <thead class="border-b border-black">
                            <tr>
                                <th class="text-left px-1 h-5">UND</th>
                                <th class="text-left px-1 h-5">NOMBRE</th>
                                <th class="text-right px-1 h-5">PRECIO</th>
                                <th class="text-right px-1 h-5">IMPORTE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="articulo in carrito">
                                <tr class="text-black h-5">
                                    <td class="text-right px-1 h-5" x-text="articulo.cantidad"></td>
                                    <td class="text-left px-1 h-5 truncate" x-text="articulo.name"></td>
                                    <td class="text-right px-1 h-5" x-text="(articulo.precio).toFixed(2) + '€'"></td>
                                    <td class="text-right px-1 h-5" x-text="(articulo.precio * articulo.cantidad).toFixed(2) + '€'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <table class="w-full border-b-4 text-xs">
                        <thead>
                            <tr>
                                <th class="text-right">IMP</th>
                                <th class="text-right">BASE</th>
                                <th class="text-right">CUOTA</th>
                                <th class="text-right">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(value, index) in tipoIVA()">
                                <tr>
                                    <td class="text-right" x-text="index + '%'"></td>
                                    <td class="text-right" x-text="value.base.toFixed(2) + '€'"></td>
                                    <td class="text-right" x-text="value.cuota.toFixed(2) + '€'"></td>
                                    <td class="text-right" x-text="value.total.toFixed(2) + '€'"></td>
                                </tr>
                            </template>
                            <tr>
                                <td></td>
                                <td></td>
                                <td class="font-bold text-right border-t border-black">TOTAL</td>
                                <td class="font-bold text-right border-t border-black" x-text="(totalSinDesglosar).toFixed(2) + '€'"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Footer -->
                <div class="border-t border-black text-xs mt-3">
                    <div class="flex justify-between mb-2">
                        <div>
                            <span>ENTREGADO</span>
                            <span class="border-b-2 border-l-skin-primary ml-1" x-text="dineroEntregado + '€'"></span>
                        </div>
                        <div>
                            <span>CAMBIO</span>
                            <span class="border-b-2 border-l-skin-primary ml-1" x-text="cambio + '€'"></span>
                        </div>
                    </div>
                    <div class="flex mb-2">
                        <span class="mr-2">Num. Lin. Factura</span>
                        <span x-text="elementosCarrito()"></span>
                    </div>
                    <div class="flex mb-2">
                        <span class="mr-2">Vendedor</span>
                        <span x-text="nombreVendedor"></span>
                    </div>
                    <div class="w-full flex flex-col items-center justify-center text-center">
                        <span class="my-2">**** IMPUESTOS INCLUIDOS ****</span>
                        <span class="my-2">GRACIAS POR SU COMPRA</span>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>