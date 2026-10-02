<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (in_array(DB::getDriverName(), ['sqlite', 'pgsql'], true)) {
            Schema::table('feedback', function (Blueprint $table) {
                if (!Schema::hasColumn('feedback', 'sqd0')) {
                    $table->string('sqd0')->nullable()->after('comments');
                }
                if (!Schema::hasColumn('feedback', 'sqd1')) {
                    $table->string('sqd1')->nullable()->after('sqd0');
                }
                if (!Schema::hasColumn('feedback', 'sqd2')) {
                    $table->string('sqd2')->nullable()->after('sqd1');
                }
                if (!Schema::hasColumn('feedback', 'sqd3')) {
                    $table->string('sqd3')->nullable()->after('sqd2');
                }
                if (!Schema::hasColumn('feedback', 'sqd4')) {
                    $table->string('sqd4')->nullable()->after('sqd3');
                }
                if (!Schema::hasColumn('feedback', 'sqd5')) {
                    $table->string('sqd5')->nullable()->after('sqd4');
                }
                if (!Schema::hasColumn('feedback', 'sqd6')) {
                    $table->string('sqd6')->nullable()->after('sqd5');
                }
                if (!Schema::hasColumn('feedback', 'sqd7')) {
                    $table->string('sqd7')->nullable()->after('sqd6');
                }
                if (!Schema::hasColumn('feedback', 'sqd8')) {
                    $table->string('sqd8')->nullable()->after('sqd7');
                }
                if (!Schema::hasColumn('feedback', 'suggestions')) {
                    $table->text('suggestions')->nullable()->after('sqd8');
                }
            });

            return;
        }

        Schema::table('feedback', function (Blueprint $table) {
            if (!Schema::hasColumn('feedback', 'sqd0')) {
                $table->string('sqd0')->nullable()->after('comments');
            }
            if (!Schema::hasColumn('feedback', 'sqd1')) {
                $table->string('sqd1')->nullable()->after('sqd0');
            }
            if (!Schema::hasColumn('feedback', 'sqd2')) {
                $table->string('sqd2')->nullable()->after('sqd1');
            }
            if (!Schema::hasColumn('feedback', 'sqd3')) {
                $table->string('sqd3')->nullable()->after('sqd2');
            }
            if (!Schema::hasColumn('feedback', 'sqd4')) {
                $table->string('sqd4')->nullable()->after('sqd3');
            }
            if (!Schema::hasColumn('feedback', 'sqd5')) {
                $table->string('sqd5')->nullable()->after('sqd4');
            }
            if (!Schema::hasColumn('feedback', 'sqd6')) {
                $table->string('sqd6')->nullable()->after('sqd5');
            }
            if (!Schema::hasColumn('feedback', 'sqd7')) {
                $table->string('sqd7')->nullable()->after('sqd6');
            }
            if (!Schema::hasColumn('feedback', 'sqd8')) {
                $table->string('sqd8')->nullable()->after('sqd7');
            }
            if (!Schema::hasColumn('feedback', 'suggestions')) {
                $table->text('suggestions')->nullable()->after('sqd8');
            }
        });

        DB::statement('ALTER TABLE feedback MODIFY COLUMN rating TINYINT UNSIGNED NULL');
    }

    public function down(): void
    {
        if (in_array(DB::getDriverName(), ['sqlite', 'pgsql'], true)) {
            Schema::table('feedback', function (Blueprint $table) {
                $table->dropColumn(['sqd0', 'sqd1', 'sqd2', 'sqd3', 'sqd4', 'sqd5', 'sqd6', 'sqd7', 'sqd8', 'suggestions']);
            });

            return;
        }

        Schema::table('feedback', function (Blueprint $table) {
            $table->dropColumn(['sqd0', 'sqd1', 'sqd2', 'sqd3', 'sqd4', 'sqd5', 'sqd6', 'sqd7', 'sqd8', 'suggestions']);
        });
        DB::statement('ALTER TABLE feedback MODIFY COLUMN rating TINYINT UNSIGNED NOT NULL');
    }
};
