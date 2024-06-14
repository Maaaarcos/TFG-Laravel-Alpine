<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User; 
use Carbon\Carbon;

class Fichar extends Component
{
    public $hora_inicio;
    public $hora_fin;
    public $tiempo_total_jornada;
    public $horas_totales;

    public function mount()
    {
        $this->hora_inicio = now();
    }

    public function fichar()
    {
        $user = auth()->user();
        $user->hora_inicio = now();
        $user->save();

        $this->hora_inicio = $user->hora_inicio;
    }
    public static function getUserName()
    {
        return auth()->user()->name;
    }

    public function desfichar()
    {
        $user = auth()->user();
        $user->hora_fin = now();
        $user->save();

        $this->hora_fin = $user->hora_fin;
        $this->calcularHorasTotales();

        $horaInicio = Carbon::parse($user->hora_inicio);
        $horaFin = Carbon::parse($user->hora_fin);

        $diff = $horaInicio->diff($horaFin);
        $tiempoTotalJornada = sprintf('%02d:%02d:%02d', $diff->h, $diff->i, $diff->s);

        $this->tiempo_total_jornada = $tiempoTotalJornada;
    }

    public function calcularHorasTotales()
    {
        $user = auth()->user();
        $horaInicio = Carbon::parse($user->hora_inicio);
        
        if ($user->hora_fin) {
            $horaFin = Carbon::parse($user->hora_fin);
        } else {
            $horaFin = now();
        }
    
        $diff = $horaInicio->diff($horaFin);
        $horasNuevas = sprintf('%02d:%02d:%02d', $diff->h, $diff->i, $diff->s);
    
        $horasTotalesAnteriores = Carbon::parse($user->horas_totales);
        $horasTotalesNuevas = $horasTotalesAnteriores->addHours($diff->h)->addMinutes($diff->i)->addSeconds($diff->s);
        $this->horas_totales = $horasTotalesNuevas->format('H:i:s');
    
        $user->horas_totales = $this->horas_totales;
        $user->save();
    }

    public function render()
    {
        return view('livewire.fichar');
    }
}
