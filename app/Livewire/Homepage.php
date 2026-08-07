<?php

namespace App\Livewire;

use Livewire\Component;

class Homepage extends Component
{
    public $isMobileMenuOpen = false;

    public function toggleMobileMenu()
    {
        $this->isMobileMenuOpen = !$this->isMobileMenuOpen;
    }

    public function render()
    {
        return view('livewire.homepage');
    }
}
