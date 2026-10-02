<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->decimal('rating', 2, 1)->nullable()->change();
        });
    }

    public function down(): void
    {
        if (DB::table('feedback')->whereNotNull('rating')->whereRaw('rating <> FLOOR(rating)')->exists()) {
            throw new RuntimeException('Cannot convert half-star feedback ratings to integers without losing data.');
        }

        Schema::table('feedback', function (Blueprint $table) {
            $table->integer('rating')->nullable()->change();
        });
    }
};
