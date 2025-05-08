<?php

namespace App\Enums\Reservation;

use App\Traits\HasEnumLabels;

enum Status: int
{
    use HasEnumLabels;

    case Rejected = 0;
    case Pending = 1;
    case Approved = 2;

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
