<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Caja;
use App\Models\MovimientosArqueo;


class Arqueo extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'saldo_inicial',
        'saldo_efectivo',
        'saldo_tarjeta',
        'saldo_final',
        'saldo_total',
        'caja_id',
    ];

    protected $dates = [
        'fecha',
    ];

    // Relación con Caja
    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    // Relación con MovimientosArqueo
    public function movimientosArqueo()
    {
        return $this->hasMany(MovimientosArqueo::class);
    }
}
