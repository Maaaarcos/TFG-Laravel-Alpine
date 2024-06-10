<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Arqueo;
use App\Models\MovimientosArqueo;

class Caja extends Model
{
    use HasFactory;

    // Especifica los campos que se pueden asignar en masa
    protected $fillable = [
        'name',
    ];

    // Relación con Arqueo
    public function arqueos()
    {
        return $this->hasMany(Arqueo::class);
    }

    // Relación con MovimientosArqueo
    public function movimientosArqueo()
    {
        return $this->hasMany(MovimientosArqueo::class);
    }
}
