<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('school')->nullable()->after('student_id');
            $table->string('professional_title')->nullable()->after('school');
            $table->text('educational_background')->nullable()->after('professional_title');
            $table->text('professional_credentials')->nullable()->after('educational_background');
            $table->text('certifications')->nullable()->after('professional_credentials');
            $table->text('trainings')->nullable()->after('certifications');
            $table->text('areas_of_expertise')->nullable()->after('trainings');
            $table->text('professional_experience')->nullable()->after('areas_of_expertise');
            $table->text('professional_biography')->nullable()->after('professional_experience');
            $table->string('office_location')->nullable()->after('professional_biography');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'school',
                'professional_title',
                'educational_background',
                'professional_credentials',
                'certifications',
                'trainings',
                'areas_of_expertise',
                'professional_experience',
                'professional_biography',
                'office_location',
            ]);
        });
    }
};