<?php

use App\Models\Room;
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
        Schema::create((new Room)->getTable(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamp('initial_availability_time')->nullable()->comment('Servirá (A futuro) para dar la hora de disponibilidad inicial');
            $table->timestamp('final_availability_time')->nullable()->comment('Servirá (A futuro) para dar la hora de disponibilidad final');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists((new Room)->getTable());
    }
};
