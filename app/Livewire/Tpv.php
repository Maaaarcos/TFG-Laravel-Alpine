<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Iva;
use App\Models\Producto;
use App\Models\Categoria;

class Tpv extends Component
{
    public $productos =[];
    public $categorias =[];
    public $iva =[];

    public function mount()
{
    $this->productos = Producto::select('id','nombre', 'precio', 'imagen_url', 'iva_id')->with('categoria')->get()->keyBy('id')->toArray();
    $this->categorias = Categoria::select('id','nombre')->get()->keyBy('id')->toArray();
    $this->iva = Iva::select('id','qty')->get()->keyBy('id')->toArray();

    // dd($this->productos, $this->categorias, $this->iva);
}
    
    public function render()
    {

        return view('livewire.tpv');
    }
}
