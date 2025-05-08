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

    /**
     * Obtiene las etiquetas personalizadas para cada estado
     *
     * @return array<int, string>
     */
    public static function getLabels(): array
    {
        return [
            self::NotAvailable->value => 'No Disponible',
            self::Available->value => 'Disponible',
        ];
    }
}
