<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_severity_check');
            DB::statement('ALTER TABLE appointments ALTER COLUMN severity DROP DEFAULT');
            DB::statement('ALTER TABLE appointments ALTER COLUMN severity TYPE varchar(255)');
            DB::statement("UPDATE appointments SET severity = 'moderate' WHERE severity = 'medium'");
            DB::statement('ALTER TABLE appointments ALTER COLUMN severity DROP NOT NULL');
            DB::statement("ALTER TABLE appointments ALTER COLUMN severity SET DEFAULT 'not_assessed'");
            DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_severity_check CHECK (severity IN ('not_assessed', 'low', 'moderate', 'high'))");

            return;
        }

        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('severity', ['not_assessed', 'low', 'moderate', 'high'])
                  ->default('not_assessed')
                  ->nullable()
                  ->after('purpose')
                  ->change();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE appointments DROP CONSTRAINT IF EXISTS appointments_severity_check');
            DB::statement('ALTER TABLE appointments ALTER COLUMN severity DROP DEFAULT');
            DB::statement("UPDATE appointments SET severity = 'low' WHERE severity = 'not_assessed'");
            DB::statement("UPDATE appointments SET severity = 'medium' WHERE severity = 'moderate'");
            DB::statement('ALTER TABLE appointments ALTER COLUMN severity TYPE varchar(255)');
            DB::statement('ALTER TABLE appointments ALTER COLUMN severity SET NOT NULL');
            DB::statement("ALTER TABLE appointments ALTER COLUMN severity SET DEFAULT 'low'");
            DB::statement("ALTER TABLE appointments ADD CONSTRAINT appointments_severity_check CHECK (severity IN ('low', 'medium', 'high'))");

            return;
        }

        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('severity', ['low', 'medium', 'high'])
                  ->default('low')
                  ->nullable(false)
                  ->change();
        });
    }
};
