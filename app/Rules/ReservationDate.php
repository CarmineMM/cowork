<?php

namespace App\Rules;

use App\Models\Reservation;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validación para la fecha de la reservación que no este a la misma hora de otra reservación
 */
class ReservationDate implements ValidationRule, DataAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // NOTA: En el futuro se podría hacer uso del end_reservation (creando un field nuevo)
        // Verificar el end_reservation, ayudara a que si en un futuro se desea hacer ese field nuevo,
        // no haya que modificar esta lógica. Sin embargo si habría que agregarlo a las rules del Request.
        $end_reservation = $this->data['end_reservation'] ?? Carbon::parse($this->data['start_reservation'])->addHour();


        // Verificar si existe alguna reservación que sea al mismo horario
        $existing_reservation = Reservation::where('room_id', $this->data['room_id'])
            ->whereDate('end_reservation', '>=', $end_reservation)
            ->exists();

        if ($existing_reservation) {
            $fail('La sala ya está reservada para este horario.');
        }
    }
}
