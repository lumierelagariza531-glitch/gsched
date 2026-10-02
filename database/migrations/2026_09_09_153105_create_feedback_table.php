<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('comments')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['appointment_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};