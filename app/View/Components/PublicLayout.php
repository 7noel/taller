<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Layout para páginas públicas del cliente (portal del vehículo).
 * Sin sidebar, mobile-first, sin autenticación.
 */
class PublicLayout extends Component
{
    public function render(): View
    {
        return view('layouts.public');
    }
}
