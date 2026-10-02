<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('facebook_profile_url', 2048)->nullable();
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->string('facebook_profile_url', 2048)->nullable();
        });

        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        if ($adminRoleId) {
            DB::table('users')
                ->where('role_id', $adminRoleId)
                ->whereNull('facebook_profile_url')
                ->update(['facebook_profile_url' => 'https://www.facebook.com/jocelyn.caing']);
        }
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('facebook_profile_url');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('facebook_profile_url');
        });
    }
};
