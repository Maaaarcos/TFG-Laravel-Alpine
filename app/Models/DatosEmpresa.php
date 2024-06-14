<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatosEmpresa extends Model
{
    use HasFactory;

    // Define the table name if it does not follow Laravel's naming convention
    protected $table = 'datos_empresa';

    // Specify the fields that can be mass-assigned
    protected $fillable = [
        'tercero_id',
        'nombre',
        'direccion',
        'provincia',
        'telefono',
        'email',
        'ruc',
        'tipo_empresa',
        'actividad_economica',
        'ciudad',
        'codigo_postal',
        'nif',
    ];

    // Define the relationship with the Tercero model
    public function tercero()
    {
        return $this->belongsTo(Tercero::class);
    }
}
