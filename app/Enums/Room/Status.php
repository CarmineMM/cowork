<?php

namespace App\Enums\Room;

/**
 * Estado de la sala
 */
enum Status: int
{
    case NotAvailable = 0; # No disponible
    case Available = 1;    # Sala disponible para reservas
    //.. Otros status como, en reparaciones...
}
