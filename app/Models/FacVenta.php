<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tercero;
use App\Models\FormasPago;

class FacVenta extends Model
{
    use HasFactory;

    // Especifica los campos que se pueden asignar en masa
    protected $fillable = [
        'name',
        'tercero_id',
        'fecha',
        'forma_pago_id',
        'base_imp',
        'total_iva',
        'total',
        'caja_id',
    ];

    // Relación con Tercero
    public function tercero()
    {
        return $this->belongsTo(Tercero::class);
    }

    // Relación con FormaPago
    public function formaPago()
    {
        return $this->belongsTo(FormasPago::class, 'forma_pago_id');
    }
}
