<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * Variante de layout guest: 'centered' (por defecto) o 'split' (pantalla dividida).
     */
    public string $variant;

    public function __construct(string $variant = 'centered')
    {
        $this->variant = $variant;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest', ['variant' => $this->variant]);
    }
}
