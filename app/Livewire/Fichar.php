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
        $user = auth()->user();
        if ($user->hora_inicio && !$user->hora_fin) {
            $this->hora_inicio = Carbon::parse($user->hora_inicio);
        } else {
            $this->hora_inicio = null;
        }
        $this->hora_fin = $user->hora_fin ? Carbon::parse($user->hora_fin) : null;
        $this->horas_totales = $user->horas_totales ? Carbon::parse($user->horas_totales)->format('H:i:s') : '00:00:00';
    }

    public function fichar()
    {
        $user = auth()->user();
        $user->hora_inicio = now();
        $user->hora_fin = null; // Reset hora_fin when starting a new session
        $user->save();

        $this->hora_inicio = $user->hora_inicio;
        $this->hora_fin = null;
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
    
        $horasTotalesAnteriores = $user->horas_totales ? Carbon::parse($user->horas_totales) : Carbon::parse('00:00:00');
        $horasTotalesNuevas = $horasTotalesAnteriores->addHours($diff->h)->addMinutes($diff->i)->addSeconds($diff->s);
        $this->horas_totales = $horasTotalesNuevas->format('H:i:s');
    
        $user->horas_totales = $this->horas_totales;
        $user->save();
    }

    public function render()
    {
        return view('livewire.fichar', [
            'fichado' => $this->hora_inicio && !$this->hora_fin,
        ]);
    }
}
