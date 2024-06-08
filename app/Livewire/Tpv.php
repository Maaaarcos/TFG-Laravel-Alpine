<?php

    namespace App\Livewire;
    
    use App\Models\Categoria;
    use App\Models\Iva;
    use App\Models\Producto;
    use Livewire\Component;
    use App\Models\FacVenta;
    use App\Models\FormasPago;
    use App\Models\Conf;
    use App\Models\Provincia;
    use App\Models\Arqueo;
    use App\Models\Caja;
    use App\Models\User;
    use App\Models\TarjetasRegalo;
    use App\Models\ArqueoMove;
    use App\Models\SaldoInicialTarjetaRegalo;
    use App\Models\FacturacionRel;
    use App\Models\FacturacionLineasRel;
    use App\Models\FacVentaLinea;
    use App\Models\Pais;
    use App\Models\View;
    use Illuminate\Support\Facades\Session;
    use FontLib\Table\Type\name;
    use Carbon\Carbon;
    
    class Tpv extends Component
    {
        public $productos = [];
        public $categorias = [];
        public $iva = [];
        public $terceros = [];
        public $pagos = [];
        public $valoresRecibido;
        public $terceroSeleccionado;
        public $formaPagoSeleccionada;
        public $datosEmpresa = [];
        public $provincia = [];
        public $factura;
        public $facturas = [];
        public $getNextRef;
        public $lastArqueo;
        public $arqueos = [];
        public $arqueo_dia = ['hoy' => [], 'ayer' => []];
        public $usuarios = [];
        public $cajas = [];
        public $movimientos_arqueo = [];
        public $arqueoDisponible;
        public $tarjeta_regalo = [];
        public $SaldoIncial;
        public $id_caja;
        public $nuevoArqueo;
        public $selectCaja;
        public $caja;
        public $cajaSeleccionada;
        public $saldoInicial;
        public $saldoTR = 0;
        public $listadoFacturas = [];
        public $billetes = [];
        public $saldoEsperado = 0;
        public $pagosEfectivo = 0;
        public $pagosTarjeta = 0;
        public $conf = [];
        public $valores_fijos_TR = [];
        public $lineas_factura = [];
        public $prodFiltrados = [];
        public $historialDev = [];
        public $paises = [];
        public $provincias = [];
    
        protected $listeners = ['cajaSeleccionada' => 'selectCaja'];
    
    
        public function mount()
        {
            $this->productos = Producto::select('id', 'nombre', 'precio', 'imagen_url', 'iva_id', 'categoria_id', 'stock')->where('se_vende', '=', 1)->with('categoria')->get()->keyBy('id')->toArray();
            $this->categorias = Categoria::select('id', 'nombre', 'imagen_url')->get()->keyBy('id')->toArray();
            $this->iva = Iva::select('id', 'qty')->get()->keyBy('id')->toArray();
            // $this->usuarios = User::select('id', 'name')->get()->pluck('name')->toArray();

            //$this->terceros = Tercero::select('id', 'nombre)->get()->keyBy('id')->toArray();
            //$this->pagos = FormasPago::select('id', 'name', 'tipo_id', 'banco_id')->get()->keyBy('id')->toArray();
            //$this->datosEmpresa = Conf::select('key', 'value')->get()->keyBy('key')->toArray();
            //$this->provincia = Provincia::select('id', 'name')->get()->keyBy('id')->toArray();
            //$this->factura = FacVenta::orderBy('id', 'desc')->first();
            //$this->facturas = FacVenta::select('id', 'name', 'tercero_id', 'fecha', 'forma_pago_id', 'base_imp', 'total_iva', 'total')->get()->keyBy('id')->toArray();
            //$this->arqueos = Arqueo::select('id', 'fecha', 'saldo_inicial', 'saldo_efectivo', 'saldo_tarjeta', 'caja_id', 'saldo_total', 'saldo_final')->orderBy('fecha', 'DESC')->get()->keyBy('id')->toArray();
            //$this->movimientos_arqueo = ArqueoMove::select('id', 'arqueo_id', 'caja_id', 'user_id', 'billetes', 'moves')->get()->keyBy('id')->toArray();
            //$this->cajas = Caja::select('id', 'name')->get()->keyBy('id')->toArray();

            
            //$this->paises = Pais::select('id', 'code', 'name', 'name_en', 'prefijo')->get()->keyBy('id')->toArray();
            //$this->provincias = Provincia::select('id', 'name')->get()->keyBy('id')->toArray();
    
            //dd($this->productos);
            //dd($this->categorias);
            //dd($this->iva);
    
    
            //$this->getNextRef = $this->factura->getNextRef();
    
            //$this->selectCaja = $this->selectCaja($this->listeners);
    
    
            //$terceroAnonimo = collect($this->terceros)->firstWhere('name', 'Anonimo');
            //if ($terceroAnonimo) {
            //    $this->terceroSeleccionado = $terceroAnonimo['id'];
            //}
    
            //$formaPago = collect($this->pagos)->first();
            //if ($formaPago) {
            //    $this->formaPagoSeleccionada = $formaPago['id'];
            //}
        }
    
    
        public function listarFacturas()
        {
            $facturas = FacVenta::select('caja_id', date('Y-m-d'))
                ->select('name', 'tercero_id', 'fecha', 'base_imp', 'total_iva', 'total', 'forma_pago_id')
                ->get();
    
            $this->listadoFacturas = $facturas;
        }
    
        //public function selectCaja($caja)
        //{
    //
        //    $this->arqueo_dia['hoy'] = Arqueo::select('id', 'fecha', 'saldo_inicial', 'saldo_efectivo', 'saldo_tarjeta', 'caja_id', 'saldo_total')
        //        ->whereDate('fecha', Carbon::today()->toDateString())
        //        ->where('caja_id', $caja)
        //        ->get()
        //        ->keyBy('id')
        //        ->toArray();
    //
        //    $this->arqueo_dia['ayer'] = Arqueo::select('id', 'fecha', 'saldo_inicial', 'saldo_efectivo', 'saldo_tarjeta', 'caja_id', 'saldo_total')
        //        ->where('caja_id', $caja)
        //        ->latest()
        //        ->get()
        //        ->keyBy('id')
        //        ->toArray();
        //}
        //saber que lineas necesito para hacer la factura
        public function filtrarLineasFactura($objectId)
        {
            $productosFiltrados = FacVentaLinea::where('object_id', $objectId)->get()->toArray();
            $this->lineas_factura = $productosFiltrados;
        }
        //saber a que factura voy a hacerle la devolucion
        public function getFacturaDevolucion($objectId)
        {
            $productosFiltrados = FacVenta::where('id', $objectId)->get()->toArray();
            $this->prodFiltrados = $productosFiltrados;
        }
    
        public function comprobarArqueo($caja_id)
        {
            $arqueos = Arqueo::whereDate('fecha', date('Y-m-d'))
                ->where('caja_id', $caja_id)
                ->exists();
    
            return $arqueos;
        }
        public function valorSaldoIncial($caja_id)
        {
            $ultimoArqueo = Arqueo::where('caja_id', $caja_id)
                ->latest('id')
                ->first();
    
    
            $saldo_inicial = $ultimoArqueo ? $ultimoArqueo->saldo_final : 10;
    
            $this->saldoInicial = $saldo_inicial;
        }
        public function valorBilletes($caja_id)
        {
            $ultimoArqueo = ArqueoMove::where('caja_id', $caja_id)
                ->latest('id')
                ->first();
    
            $billetes = $ultimoArqueo ? json_decode($ultimoArqueo->billetes, true) :
                ["1" => 0, "2" => 0, "5" => 0, "10" => 0, "20" => 0, "50" => 0, "100" => 0, "200" => 0, "500" => 0, "05" => 0, "02" => 0, "01" => 0, "005" => 0, "002" => 0, "001" => 0];
    
            return $billetes;
        }
        public function crearArqueoInicio($user_name, $caja_id, $billetes)
        {
            $user = User::where('name', $user_name)->first();
    
            $ultimoArqueo = Arqueo::where('caja_id', $caja_id)
                ->latest('id')
                ->first();
    
    
            $saldo_inicial = $ultimoArqueo ? $ultimoArqueo->saldo_final : 0;
    
            $arqueo = Arqueo::create([
                'fecha' => now(),
                'saldo_inicial' => $saldo_inicial,
                'saldo_total' => 0,
                'saldo_efectivo' => 0,
                'saldo_tarjeta' => 0,
                'saldo_final' => 0,
                'caja_id' => $caja_id,
            ]);
            ArqueoMove::create([
                'arqueo_id' => $arqueo->id,
                'caja_id' => $caja_id,
                'user_id' => $user->id,
                'billetes' => json_encode($billetes),
                'moves' => 'apertura',
            ]);
        }
        public function crearArqueoCierre($user_name, $caja_id, $billetes, $saldoInicialSiguiente, $sumaBilletes)
        {
    
            $user = User::where('name', $user_name)->first();
    
            $arqueoExistente = Arqueo::where('fecha', date('Y-m-d'))
                ->where('caja_id', $caja_id)
                ->first();
    
            $arqueoExistente->update([
                'saldo_total' => $sumaBilletes,
                'saldo_efectivo' => 0,
                'saldo_tarjeta' => 0,
                'saldo_final' => $saldoInicialSiguiente,
    
            ]);
    
            ArqueoMove::create([
                'arqueo_id' => $arqueoExistente->id,
                'caja_id' => $caja_id,
                'user_id' => $user->id,
                'billetes' => json_encode($billetes),
                'moves' => 'cierre',
            ]);
        }
    
        public function crearTarjetaRegalo($codigo, $cantidad_inicial, $fechaCaducidad, $tipo)
        {
            TarjetasRegalo::create([
                'codigo' => (int)$codigo,
                'saldo_inicial' => $cantidad_inicial,
                'saldo' => $cantidad_inicial,
                'fecha_caducidad' => $fechaCaducidad,
                'estado' => 1,
                'tipo' => $tipo,
            ]);
        }
        public function editarTarjetaRegalo($id, $estado)
        {
            $tarjetaRegalo = TarjetasRegalo::find($id);
    
            if ($tarjetaRegalo) {
    
                $tarjetaRegalo->estado = $estado;
                $tarjetaRegalo->save();
            }
        }
        public function saldoTarjetaRegalo($codigo)
        {
            $tarjetaRegalo = TarjetasRegalo::where('codigo', $codigo)->first();
    
            $saldo = $tarjetaRegalo->saldo;
            $this->saldoTR = $saldo;
        }
    
        public function crearTicket($valores, $valorIVA, $totalSinDesglosar, $totalCarrito, $usuarioName, $metodoDePago)
        {
    
            $usuario = User::where('name', $usuarioName)->first();
            $usuarioId = $usuario->id;
    
            $lineas = [];
            foreach ($valores as $value) {
                $ls =
                    [
                        'producto_id' => $value['id'],
                        'tipo' => '0',
                        'precio' => $value['precio'],
                        'qty' => $value['cantidad'],
    
    
                    ];
                array_push($lineas, $ls);
    
                $producto = Producto::find($value['id']);
                $producto->stock -= $value['cantidad'];
                $producto->save();
            }
    
            $idFormaPago = $this->formaPagoSeleccionada;
            if($metodoDePago === 'tarjeta'){
                $fp = FormasPago::firstOrCreate(
                    ['name' => 'Tarjeta'],
                    [
                        'n_pagos' => 1,
                        'tipo_id' => 'tarj',
                        'dias_vencimiento' => 0,
                        'adelantado' => 0,
                    ]
                );
                $idFormaPago = $fp->id;
            }
    
            FacVenta::createRapida(
                [
                    'tercero_id' => $this->terceroSeleccionado,
                    'forma_pago_id' => $idFormaPago,
                    'estado' => '8',
                    'base_imp' => $totalCarrito,
                    'total_iva' => $valorIVA,
                    'total' => $totalSinDesglosar,
                    'user_id' => $usuarioId,
                    'tipo' => 0,
                ],
                $lineas
            );
    
            $this->getNextRef = $this->factura->getNextRef();
    
            $this->factura = FacVenta::orderBy('id', 'desc')->first();
            $this->facturas = FacVenta::select('id', 'name', 'tercero_id', 'fecha', 'forma_pago_id', 'base_imp', 'total_iva', 'total')->get()->keyBy('id')->toArray();
        }
        public function crearDevolucion($valores, $iva, $valorIVA, $totalSinDesglosar, $totalCarrito, $usuarioName, $facturaId, $stock)
        {
    
            $usuario = User::where('name', $usuarioName)->first();
            $usuarioId = $usuario->id;
    
            $lineas = [];
            foreach ($valores as $value) {
                $ls =
                    [
                        'producto_id' => $value['id'],
                        'tipo' => '0',
                        'precio' => $value['precio'] * -1,
                        'qty' => $value['cantidad'],
                        'base_imp' => $totalCarrito * -1,
                        'total_iva' => $valorIVA * -1,
                        'total' => $totalSinDesglosar * -1,
    
                    ];
                array_push($lineas, $ls);
                if ($stock) {
                    $producto = Producto::find($value['id']);
                    $producto->stock += $value['cantidad'];
                    $producto->save();
                }
            }
    
            $abono = FacVenta::createRapida(
                [
                    'tercero_id' => $this->terceroSeleccionado,
                    'forma_pago_id' => $this->formaPagoSeleccionada,
                    'estado' => '8',
                    'user_id' => $usuarioId,
                    'tipo' => 1,
                ],
                $lineas
            );
    
            $this->getNextRef = $this->factura->getNextRef();
            FacturacionRel::create([
                "origen_id" => $facturaId, //id de la factura del array
                "tipo_origen" => 'FacVenta',
                "destino_id" => $abono->id, //id de la nueva linea que sera el abono
                "tipo_destino" => 'FacVenta',
            ]);
    
            foreach ($abono->lineas as $l) {
                $lineaOriginal = array_search($l->producto_id, array_column($this->lineas_factura, 'producto_id'));
                FacturacionLineasRel::create([
                    "origen_id" => $this->lineas_factura[$lineaOriginal]["id"], // id de la linea original del producto en la tabla FacVentaLinea lineas_factura
                    "tipo_origen" => 'FacVentaLinea',
                    "destino_id" => $l->id, // id que genero en FacVentaLinea al crear una nueva linea con el array arrayDevoluciones
                    "tipo_destino" => 'FacVentaLinea',
                ]);
            }
        }
        public function sumarValoresTablaFacVenta()
        {
            $fechaActual = date('Y-m-d');
    
            $saldo_total = FacVenta::whereDate('created_at', $fechaActual)->sum('total');
    
            $saldo_efectivo = FacVenta::where('forma_pago_id', 1)
            ->whereDate('created_at', $fechaActual)
                ->sum('total');
    
            $saldo_tarjeta = FacVenta::where('forma_pago_id', 2)
            ->whereDate('created_at', $fechaActual)
                ->sum('total');
    
            // Actualizar los campos en la tabla Arqueo
            Arqueo::whereDate('fecha', $fechaActual)
                ->update([
                    'saldo_total' => $saldo_total,
                    'saldo_efectivo' => $saldo_efectivo,
                    'saldo_tarjeta' => $saldo_tarjeta,
                ]);
            $this->saldoEsperado = $saldo_efectivo;
            $this->arqueos = Arqueo::select('id', 'fecha', 'saldo_inicial', 'saldo_efectivo', 'saldo_tarjeta', 'caja_id', 'saldo_total', 'saldo_final')->orderBy('fecha', 'DESC')->get()->keyBy('id')->toArray();
        }
    
        public function getHistorialDev($id)
        {
            $destino_id = FacturacionRel::where('origen_id', $id)
                ->where('tipo_origen', 'FacVenta')
                ->pluck('destino_id')
                ->toArray();
            $valores = FacVenta::whereIn('id', $destino_id)->get()->toArray();
            $lineas = FacVentaLinea::whereIn('object_id', array_column($valores, 'id'))->get()->toArray();
    
            // Asignar los valores a la propiedad fechaHistorialDev
            $this->historialDev = [
                'valores' => $valores,
                'lineas' => $lineas
            ];
        }
        public function getHistorialCambios($id)
        {
            $destino_id = FacturacionRel::where('origen_id', $id)
                ->where('tipo_origen', 'FacVenta')
                ->pluck('destino_id')
                ->toArray();
            $valores = FacVenta::whereIn('id', $destino_id)->get()->toArray();
            $lineas = FacVentaLinea::whereIn('object_id', array_column($valores, 'id'))->get()->toArray();
    
            // Asignar los valores a la propiedad fechaHistorialDev
            $this->historialDev = [
                'valores' => $valores,
                'lineas' => $lineas
            ];
        }
        public function updateTarjetasRegalo()
        {
            $this->tarjeta_regalo = TarjetasRegalo::select('id', 'codigo', 'saldo_inicial', 'saldo', 'fecha_caducidad', 'estado', 'tipo')->get()->keyBy('id')->toArray();
        }
        public function updateVentas()
        {
            $this->facturas = FacVenta::select('id', 'name', 'tercero_id', 'fecha', 'forma_pago_id', 'base_imp', 'total_iva', 'total')->get()->keyBy('id')->toArray();
        }
    
        public function generarPdf($id){
            $this->emitTo('functions.generar-p-d-f','generarPdf',false,false,$id);
        }
    
        public function crearTercero($name, $nif = null, $email = null, $pais_id = null, $provincia_id = null, $ciudad = null, $direccion = null, $telefono1 = null, $cod_postal = null)
        {
            Tercero::create([
                'name' => $name,
                'nif' => $nif,
                'tipo' => 'cli',
                'email' => $email,
                'estado' => 1,
                'pais_id' => $pais_id,
                'provincia_id' => $provincia_id,
                'ciudad' => $ciudad,
                'direccion' => $direccion,
                'telefono1' => $telefono1,
                'cod_postal' => $cod_postal,
            ]);
            $this->terceros = Tercero::select('id', 'name')->get()->keyBy('id')->toArray();
        }
        public function render()
        {
            // $this->sumarValoresTablaFacVenta();
            return view('livewire.tpv');
        }
    }
