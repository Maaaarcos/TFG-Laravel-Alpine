<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Categoria;
use App\Models\Iva;


class Producto extends Model
{   
    use HasFactory;

    protected $table = 'productos';
    protected $fillable = ['nombre', 'precio', 'descripcion'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function iva()
    {
        return $this->belongsTo(Iva::class);
    }

    public function getNombreCategoriaAtributo()
    {
        return $this->categoria->nombre;
    }

    public function getNombreIvaAtributo()
    {
        return $this->iva->qty;
    }

    public function getNombrePrecioAtributo()
    {
        return number_format($this->precio, 2, ',', '.');
    }

    public function setPrecioAttribute($value)
    {
        $this->attributes['precio'] = str_replace(',', '.', $value);
    }

}