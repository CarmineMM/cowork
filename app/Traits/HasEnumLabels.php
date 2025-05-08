<?php

namespace App\Traits;

trait HasEnumLabels
{
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
}
