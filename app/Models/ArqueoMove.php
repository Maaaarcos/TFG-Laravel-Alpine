<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Caja;
use App\Models\User;
use App\Models\Arqueo;

class ArqueoMove extends Model
{
    use HasFactory;

    protected $fillable = [
        'arqueo_id',
        'caja_id',
        'user_id',
        'billetes',
        'moves',
    ];

    // Relación con Arqueo
    public function arqueo()
    {
        return $this->belongsTo(Arqueo::class);
    }

    // Relación con Caja
    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
