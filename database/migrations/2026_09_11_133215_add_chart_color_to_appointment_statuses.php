<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointment_statuses', function (Blueprint $table) {
            $table->string('chart_color', 7)->default('#6c757d')->after('color');
        });
    }

    public function down(): void
    {
        Schema::table('appointment_statuses', function (Blueprint $table) {
            $table->dropColumn('chart_color');
        });
    }
};