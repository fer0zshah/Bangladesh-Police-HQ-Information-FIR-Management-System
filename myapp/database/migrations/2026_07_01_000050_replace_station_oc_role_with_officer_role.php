<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'station_oc', 'officer', 'citizen'])
                ->default('citizen')
                ->change();
        });

        DB::table('users')->where('role', 'station_oc')->update(['role' => 'officer']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'officer', 'citizen'])
                ->default('citizen')
                ->change();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'station_oc', 'officer', 'citizen'])
                ->default('citizen')
                ->change();
        });

        DB::table('users')->where('role', 'officer')->update(['role' => 'station_oc']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'station_oc', 'citizen'])
                ->default('citizen')
                ->change();
        });
    }
};
