<?php

namespace App\Livewire;

use App\Models\Categoria;
use App\Models\Iva;
use App\Models\Producto;
use Livewire\Component;
use App\Models\FacVenta;
use App\Models\Tercero;
use App\Models\FormasPago;
use App\Models\Arqueo;
use App\Models\Caja;
use App\Models\User;
use App\Models\ArqueoMove;
use App\Models\DatosEmpresa;
use App\Models\FacVentaLinea;
use App\Models\View;
use Illuminate\Support\Facades\Session;
use FontLib\Table\Type\name;
use Carbon\Carbon;



use Livewire\WithFileUploads;
class Tpv extends Component
{
    use WithFileUploads; 
    
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
    public $SaldoIncial;
    public $selectCaja;
    public $caja;
    public $cajaSeleccionada;
    public $saldoInicial;
    public $saldoEsperado;
    public $listadoFacturas = [];
    public $billetes = [];
    public $pagosEfectivo = 0;
    public $pagosTarjeta = 0;
    public $lineas_factura = [];
    public $user;
    public $sumaTotalTarjeta = 0;
    public $sumaTotalEfectivo = 0;
    public $saldo_inicialDS = 0;

    protected $listeners = ['cajaSeleccionada' => 'selectCaja'];

    public function mount()
    {
        $this->productos = Producto::select('id', 'nombre', 'precio', 'imagen_url', 'iva_id', 'categoria_id', 'stock')->where('se_vende', '=', 1)->where('stock', '>', 0)->with('categoria')->get()->keyBy('id')->toArray();
        $this->categorias = Categoria::select('id', 'nombre', 'imagen_url')->get()->keyBy('id')->toArray();
        $this->iva = Iva::select('id', 'qty')->get()->keyBy('id')->toArray();
        $this->usuarios = User::select('id', 'name')->get()->pluck('name')->toArray();
        $this->terceros = Tercero::select('id', 'nombre')->get()->keyBy('id')->toArray();
        $this->pagos = FormasPago::select('id', 'name')->get()->keyBy('id')->toArray();
        // $this->datosEmpresa = DatosEmpresa::select('id', 'tercero_id', 'nombre', 'direccion', 'provincia', 'telefono', 'email', 'ruc', 'tipo_empresa', 'actividad_economica', 'ciudad', 'codigo_postal', 'nif')->first()->toArray();
        $this->factura = FacVenta::orderBy('id', 'desc')->first();
        $this->facturas = FacVenta::select('id', 'name', 'tercero_id', 'fecha', 'forma_pago_id', 'base_imp', 'total_iva', 'total')->get()->keyBy('id')->toArray();
        $this->arqueos = Arqueo::select('id', 'fecha', 'saldo_inicial', 'saldo_efectivo', 'saldo_tarjeta', 'caja_id', 'saldo_total', 'saldo_final')->orderBy('fecha', 'DESC')->get()->keyBy('id')->toArray();
        $this->movimientos_arqueo = ArqueoMove::select('id', 'arqueo_id', 'caja_id', 'user_id', 'billetes', 'moves')->get()->keyBy('id')->toArray();
        $this->cajas = Caja::select('id', 'name')->get()->keyBy('id')->toArray();
        $this->user = Fichar::getUserName();

        $this->obtenerSiguienteNumeroFactura();
    }


    public function obtenerSiguienteNumeroFactura()
    {
        // Obtener el año actual en formato de dos dígitos (YY)
        $year = now()->format('y');
        
        // Construir el prefijo de la factura (FSYY-)
        $prefix = 'FS' . $year . '-';
        
        // Obtener la última factura que cumpla con el prefijo ordenándola por ID descendente
        $lastInvoice = FacVenta::where('name', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        
        // Obtener el número secuencial (XXXX) sumando 1 al último número encontrado
        $sequence = $lastInvoice ? intval(substr($lastInvoice->name, -4)) + 1 : 1;
        
        // Construir el nombre completo de la próxima factura
        $this->getNextRef = $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
        
    }
    

    public function updateSumaTotalEfectivo($caja_id)
    {
        $fecha_actual = now()->format('Y-m-d');

        // Obtener la suma total de ventas pagadas en efectivo para la fecha y caja especificadas
        $sumaTotalEfectivo = FacVenta::whereDate('fecha', $fecha_actual)
                                    ->where('caja_id', $caja_id)
                                    ->where('forma_pago_id', 1) // ID 1 para efectivo
                                    ->sum('total');

        // Actualizar el registro de arqueo existente
        $arqueoExistente = Arqueo::where('fecha', $fecha_actual)
                                ->where('caja_id', $caja_id)
                                ->first();

        if ($arqueoExistente) {
            $arqueoExistente->update([
                'saldo_efectivo' => $sumaTotalEfectivo,
                'saldo_total' => $arqueoExistente->saldo_tarjeta + $sumaTotalEfectivo,
            ]);
        }
        $this->sumaTotalEfectivo = $sumaTotalEfectivo;
    }


    public function updateSumaTotalTarjeta($caja_id)
    {
        $fecha_actual = now()->format('Y-m-d');

        // Obtener la suma total de ventas pagadas con tarjeta para la fecha y caja especificadas
        $sumaTotalTarjeta = FacVenta::whereDate('fecha', $fecha_actual)
                                    ->where('caja_id', $caja_id)
                                    ->where('forma_pago_id', 2) // ID 2 para tarjeta
                                    ->sum('total');

        // Actualizar el registro de arqueo existente
        $arqueoExistente = Arqueo::where('fecha', $fecha_actual)
                                ->where('caja_id', $caja_id)
                                ->first();

        if ($arqueoExistente) {
            $arqueoExistente->update([
                'saldo_tarjeta' => $sumaTotalTarjeta,
                'saldo_total' => $arqueoExistente->saldo_efectivo + $sumaTotalTarjeta, 
            ]);
        }
        $this->sumaTotalTarjeta = $sumaTotalTarjeta;
    }


    public function updateTotalesArqueo($caja_id)
    {
        $fecha_actual = now()->format('Y-m-d');

        // Actualizar el registro de arqueo existente
        $arqueoExistente = Arqueo::where('fecha', $fecha_actual)
                                ->where('caja_id', $caja_id)
                                ->first();

                //dd($arqueoExistente->saldo_efectivo, $this->saldoInicial);
        if ($arqueoExistente) {
            // Actualizar el valor de saldo_final 
            $arqueoExistente->update([
                'saldo_final' => $arqueoExistente->saldo_efectivo + $this->saldoInicial,
            ]);
        }
        $this->saldoEsperado = $arqueoExistente->saldo_efectivo + $this->saldoInicial;
    
    }


    public function cierreCajaAndUpdate($caja_id, $sumaBilletes){
        $fecha_actual = now()->format('Y-m-d');

        // Actualizar el registro de arqueo existente
        $arqueoExistente = Arqueo::where('fecha', $fecha_actual)
                                ->where('caja_id', $caja_id)
                                ->first();
        //dd($fecha_actual, $caja_id, $sumaTotalTarjeta, $arqueoExistente);

        if ($arqueoExistente) {
            // Actualizar el valor de saldo_final para que sea igual a saldo_total
            $arqueoExistente->update([
                'saldo_final' => $sumaBilletes,
            ]);
        }
    }


    public function listarFacturas()
    {
        $facturas = FacVenta::select('caja_id', date('Y-m-d'))
            ->select('name', 'tercero_id', 'fecha', 'base_imp', 'total_iva', 'total', 'forma_pago_id')
            ->get();

        $this->listadoFacturas = $facturas;
    }


    public function selectCaja($caja)
    {

        $this->arqueo_dia['hoy'] = Arqueo::select('id', 'fecha', 'saldo_inicial', 'saldo_efectivo', 'saldo_tarjeta', 'caja_id', 'saldo_total')
            ->whereDate('fecha', Carbon::today()->toDateString())
            ->where('caja_id', $caja)
            ->get()
            ->keyBy('id')
            ->toArray();

        $this->arqueo_dia['ayer'] = Arqueo::select('id', 'fecha', 'saldo_inicial', 'saldo_efectivo', 'saldo_tarjeta', 'caja_id', 'saldo_total')
            ->where('caja_id', $caja)
            ->latest()
            ->get()
            ->keyBy('id')
            ->toArray();
    }


    //saber que lineas necesito para hacer la factura
    public function filtrarLineasFactura($objectId)
    {
        $productosFiltrados = FacVentaLinea::where('object_id', $objectId)->get()->toArray();
        $this->lineas_factura = $productosFiltrados;
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

        $saldo_inicial = $ultimoArqueo ? $ultimoArqueo->saldo_final : 0;

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


    public function crearArqueoInicio($caja_id, $billetes)
    {
        $user = User::where('name', $this->user)->first();

        $ultimoArqueo = Arqueo::where('caja_id', $caja_id)
            ->latest('id')
            ->first();

        $saldo_inicial = $ultimoArqueo ? $ultimoArqueo->saldo_final : 0;

        $fecha = now()->format('Y-m-d');

        $arqueo = Arqueo::create([
            'fecha' => $fecha,
            'saldo_inicial' => $saldo_inicial,
            'saldo_efectivo' => $saldo_inicial,
            'saldo_tarjeta' => 0,
            'saldo_final' => $saldo_inicial,
            'saldo_total' => $saldo_inicial,
            'caja_id' => $caja_id,
        ]);

        $arqueo->save();

        $arqueoMove = ArqueoMove::create([
            'arqueo_id' => $arqueo->id,
            'caja_id' => $caja_id,
            'user_id' => $user->id,
            'billetes' => json_encode($billetes),
            'moves' => 'inicio',
        ]);

        $arqueoMove->save();
        $this->saldo_inicialDS = $saldo_inicial;

        $this->arqueos = Arqueo::select('id', 'fecha', 'saldo_inicial', 'saldo_efectivo', 'saldo_tarjeta', 'caja_id', 'saldo_total', 'saldo_final')->orderBy('fecha', 'DESC')->get()->keyBy('id')->toArray();
        return $arqueo;
    }


    public function crearArqueoCierre($user_name, $caja_id, $billetes, $saldoInicialSiguiente, $sumaBilletes)
    {

        $user = User::where('name', $user_name)->first();

        $arqueoExistente = Arqueo::where('fecha', date('Y-m-d'))
            ->where('caja_id', $caja_id)
            ->first();

        $arqueoExistente->update([
            'saldo_total' => $this->sumaTotalEfectivo + $this->sumaTotalTarjeta,
            'saldo_efectivo' => $this->sumaTotalEfectivo,
            'saldo_tarjeta' => $this->sumaTotalTarjeta,
            'saldo_final' => $sumaBilletes,

        ]);

        ArqueoMove::create([
            'arqueo_id' => $arqueoExistente->id,
            'caja_id' => $caja_id,
            'user_id' => $user->id,
            'billetes' => json_encode($billetes),
            'moves' => 'cierre',
        ]);
        $this->arqueos = Arqueo::select('id', 'fecha', 'saldo_inicial', 'saldo_efectivo', 'saldo_tarjeta', 'caja_id', 'saldo_total', 'saldo_final')->orderBy('fecha', 'DESC')->get()->keyBy('id')->toArray();
        // Reiniciar sumaTotalTarjeta después del arqueo de cierre
        $this->sumaTotalTarjeta = 0;
        $this->sumaTotalEfectivo = 0;
    }


    public function obtenerArqueo($caja_id)
    {
        $arqueo = Arqueo::where('caja_id', $caja_id)
            ->latest('id')
            ->first();

        return $arqueo;
    }


    public function listarMovimientosArqueo($caja_id)
    {
        $movimientos = ArqueoMove::where('caja_id', $caja_id)
            ->get()
            ->toArray();

        return $movimientos;
    }


    public function listarProductos($tipo)
    {
        $productos = Producto::where('tipo', $tipo)
            ->get()
            ->toArray();

        return $productos;
    }


    public function listarUsuarios()
    {
        $usuarios = User::select('id', 'name')
            ->get()
            ->toArray();

        return $usuarios;
    }


    public function listarCajas()
    {
        $cajas = Caja::select('id', 'name')
            ->get()
            ->toArray();

        return $cajas;
    }


    public function listarFormasPago()
    {
        $formasPago = FormasPago::select('id', 'name')
            ->get()
            ->toArray();

        return $formasPago;
    }


    public function listarTerceros()
    {
        $terceros = Tercero::select('id', 'nombre')
            ->get()
            ->toArray();

        return $terceros;
    }


    public function seleccionarTercero($tercero_id)
    {
        $this->terceroSeleccionado = $tercero_id;
    }


    public function seleccionarFormaPago($formaPago_id)
    {
        $this->formaPagoSeleccionada = $formaPago_id;
    }

    
    public function guardarFactura()
    {
        $factura = new FacVenta();
        $factura->tercero_id = $this->terceroSeleccionado;
        $factura->forma_pago_id = $this->formaPagoSeleccionada;
        $factura->fecha = now();
        $factura->base_imp = array_sum(array_column($this->lineas_factura, 'base_imp'));
        $factura->total_iva = array_sum(array_column($this->lineas_factura, 'total_iva'));
        $factura->total = $factura->base_imp + $factura->total_iva;

        $factura->save();

        foreach ($this->lineas_factura as $linea) {
            $facVentaLinea = new FacVentaLinea();
            $facVentaLinea->fac_venta_id = $factura->id;
            $facVentaLinea->producto_id = $linea['producto_id'];
            $facVentaLinea->cantidad = $linea['cantidad'];
            $facVentaLinea->precio_unitario = $linea['precio_unitario'];
            $facVentaLinea->base_imp = $linea['base_imp'];
            $facVentaLinea->total_iva = $linea['total_iva'];
            $facVentaLinea->save();
        }

        return $factura;
    }


    public function crearTicket($valores, $valorIVA, $totalSinDesglosar, $totalCarrito, $usuarioName, $metodoDePago, $caja_id)
    {

        $usuario = User::where('name', $usuarioName)->first();
        $usuarioId = $usuario->id;

        //restar stock
        foreach ($valores as $value) {

            $producto = Producto::find($value['id']);
            $producto->stock -= $value['cantidad'];
            $producto->save();

        }
        // Logica obtener nombre para factura
        $year = now()->format('y');
        $prefix = 'FS' . $year . '-';
        $lastInvoice = FacVenta::where('name', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $sequence = $lastInvoice ? intval(substr($lastInvoice->name, -4)) + 1 : 1;
        $name = $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        $fecha = now()->format('Y-m-d');

        $idFormaPago = $this->formaPagoSeleccionada;

        //pasar caja_id a int
        $caja_id = intval($caja_id);


        if($metodoDePago === 'tarjeta'){
            $fp = FormasPago::firstOrCreate(
                ['name' => 'Tarjeta']
            );
            $idFormaPago = $fp->id;
            FacVenta::create(
                [
                    'name' => $name,
                    'tercero_id' => 1,
                    'fecha' => $fecha,
                    'forma_pago_id' => $idFormaPago,
                    'base_imp' => $totalCarrito,
                    'total_iva' => $valorIVA,
                    'total' => $totalSinDesglosar,
                    'caja_id' => $caja_id,
                ]
            );
        }

        if($metodoDePago === 'efectivo'){
            $fp = FormasPago::firstOrCreate(
                ['name' => 'Efectivo']
            );
            $idFormaPago = $fp->id;
            FacVenta::create(
                [
                    'name' => $name,
                    'tercero_id' => 1,
                    'fecha' => $fecha,
                    'forma_pago_id' => $idFormaPago,
                    'base_imp' => $totalCarrito,
                    'total_iva' => $valorIVA,
                    'total' => $totalSinDesglosar,
                    'caja_id' => $caja_id,
                ]
            );
        }
        $this->getNextRef = $name;
        // Actualizar el arqueo con las sumas de efectivo y tarjeta
        $this->updateSumaTotalEfectivo($caja_id);
        $this->updateSumaTotalTarjeta($caja_id);
        $this->updateTotalesArqueo($caja_id);

        // Refresca factura y arqueo
        $this->arqueos = Arqueo::select('id', 'fecha', 'saldo_inicial', 'saldo_efectivo', 'saldo_tarjeta', 'caja_id', 'saldo_total', 'saldo_final')->orderBy('fecha', 'DESC')->get()->keyBy('id')->toArray();
        $this->factura = FacVenta::orderBy('id', 'desc')->first();
        $this->facturas = FacVenta::select('id', 'name', 'tercero_id', 'fecha', 'forma_pago_id', 'base_imp', 'total_iva', 'total')->get()->keyBy('id')->toArray();
    }

}
?>
