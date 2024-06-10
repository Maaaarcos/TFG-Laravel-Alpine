<?php

namespace App\Livewire;

use Livewire\Component;

class Test extends Component
{
    public int $count = 0;

    public function add(): void
    {
        $this->count++;
    }

    public function render()
    {
        return view('livewire.test');
    }
}
