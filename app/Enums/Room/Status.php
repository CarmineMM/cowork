<?php

namespace App\Enums\Room;

use App\Traits\HasEnumLabels;

/**
 * Estado de la sala
 */
enum Status: int
{
    use HasEnumLabels;

    case NotAvailable = 0; # No disponible
    case Available = 1;    # Sala disponible para reservas
    //.. Otros status como, en reparaciones...

    /**
     * Label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return __("room.status.{$this->value}");
    }
}
