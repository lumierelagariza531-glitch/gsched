<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('guidance_associate_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('appointment_status_id')->constrained()->onDelete('restrict');
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('purpose');
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('reschedule_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            
            $table->index(['appointment_date', 'guidance_associate_id']);
            $table->index(['student_id', 'appointment_status_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};