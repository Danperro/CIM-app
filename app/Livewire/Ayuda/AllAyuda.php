<?php

namespace App\Livewire\Ayuda;

use Livewire\Component;
use Livewire\WithPagination;

class AllAyuda extends Component
{
    use WithPagination;
    public function render()
    {
        return view('livewire.ayuda.all-ayuda');
    }
}
