<?php

namespace App\Enums\Reservation;

enum Status: int
{
    case Rejected = 0;
    case Pending = 1;
    case Approved = 2;

    /**
     * Obtiene las etiquetas personalizadas para cada estado
     *
     * @return array<int, string>
     */
    public static function getLabels(): array
    {
        return [
            self::Rejected->value => 'Rechazado',
            self::Pending->value => 'Pendiente',
            self::Approved->value => 'Aprobado',
        ];
    }
}
