<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guidance_associate_id')->constrained('users')->onDelete('cascade');
            $table->date('available_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('slot_duration')->default(30);
            $table->enum('status', ['available', 'unavailable', 'booked'])->default('available');
            $table->timestamps();
            
            $table->unique(['guidance_associate_id', 'available_date', 'start_time', 'end_time'], 'avail_unique');
            $table->index(['available_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability');
    }
};