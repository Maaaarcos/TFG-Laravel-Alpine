<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Producto;

class Iva extends Model
{
    use HasFactory;
    
    protected $table = 'ivas';

    protected $fillable = [
        'id',
        'qty',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
    public static function getByQty($qty)
    {
        return self::where('qty', $qty)->get();
    }
    public function getCantidadProductosAtributo()
    {
        return $this->productos->count();
    }

    public function getQtyAttribute($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function setQtyAttribute($value)
    {
        $this->attributes['qty'] = str_replace(',', '.', $value);
    }


}

