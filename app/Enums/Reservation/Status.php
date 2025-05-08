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
        $cases = [];

        foreach (self::cases() as $case) {
            $cases[$case->value] = $case->getLabel();
        }

        return $cases;
    }

    /**
     * Label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return __("reservation.status.{$this->value}");
    }
}
