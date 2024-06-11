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
        'saldo_tarjeta',
        'caja_id',
        'saldo_final',
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
