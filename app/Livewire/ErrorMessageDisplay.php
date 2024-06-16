<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ErrorMessageDisplay extends Component
{
    public $messages = [];

    public function mount()
    {
        if (session()->has('messages.error')) {
            $this->messages = session('messages.error');
            session()->forget('messages.error'); // Limpiar los mensajes después de obtenerlos
        }
    }

    public function render()
    {
        return view('livewire.error-message-display', [
            'messages' => $this->messages,
        ]);
    }
}
