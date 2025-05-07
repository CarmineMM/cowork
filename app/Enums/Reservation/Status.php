<?php

namespace App\Enums\Reservation;

enum Status: int
{
    case Rejected = 0;
    case Pending = 1;
    case Approved = 2;
}
