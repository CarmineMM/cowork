<?php

use App\Enums\Reservation\Status as ReservationStatus;
use App\Models\{Room, User};
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamp('start_reservation')->comment('Hora de inicio');
            $table->timestamp('end_reservation')->comment('Cuando finaliza la reservación, (Ayudara a futuro, si se desea escalar la aplicación)');
            $table->unsignedTinyInteger('status')->default(ReservationStatus::Pending->value);
            $table->foreignIdFor(Room::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
