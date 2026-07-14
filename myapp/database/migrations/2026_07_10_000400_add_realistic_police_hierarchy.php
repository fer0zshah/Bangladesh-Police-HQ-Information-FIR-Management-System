<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE stations MODIFY district VARCHAR(50) NULL');
        }

        Schema::table('stations', function (Blueprint $table) {
            if (! Schema::hasColumn('stations', 'type')) {
                $table->enum('type', ['hq', 'metropolitanHQ', 'districtHQ', 'thana'])->default('thana')->after('name');
            }

            if (! Schema::hasColumn('stations', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('type');
                $table->foreign('parent_id')->references('station_id')->on('stations')->nullOnDelete();
            }

            if (! Schema::hasColumn('stations', 'division')) {
                $table->string('division', 50)->nullable()->after('district');
            }

            if (! Schema::hasColumn('stations', 'head_rank')) {
                $table->string('head_rank', 80)->default('OC')->after('division');
            }

            if (! Schema::hasColumn('stations', 'jurisdiction')) {
                $table->string('jurisdiction')->nullable()->after('contact_number');
            }

            if (! Schema::hasColumn('stations', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('status');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'station_id')) {
                $table->unsignedBigInteger('station_id')->nullable()->after('role');
                $table->foreign('station_id')->references('station_id')->on('stations')->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'officer_id')) {
                $table->unsignedBigInteger('officer_id')->nullable()->after('station_id');
                $table->foreign('officer_id')->references('officer_id')->on('officers')->nullOnDelete();
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','officer','metro_head','district_head','station_oc','citizen') NOT NULL DEFAULT 'citizen'");
        }

        DB::table('stations')->whereNull('type')->update(['type' => 'thana']);
        DB::table('stations')->whereNull('head_rank')->update(['head_rank' => 'OC']);
        DB::statement("UPDATE stations SET is_active = CASE WHEN LOWER(status) = 'active' THEN 1 ELSE 0 END");

        $hqId = DB::table('stations')
            ->where('type', 'hq')
            ->orWhere('name', 'Bangladesh Police Headquarters')
            ->value('station_id');

        if (! $hqId) {
            $hqId = DB::table('stations')->insertGetId([
                'name' => 'Bangladesh Police Headquarters',
                'type' => 'hq',
                'parent_id' => null,
                'district' => null,
                'division' => 'Dhaka',
                'head_rank' => 'IGP',
                'address' => 'Police Headquarters, Dhaka',
                'contact_number' => null,
                'jurisdiction' => 'All of Bangladesh',
                'status' => 'Active',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ], 'station_id');
        }

        DB::table('stations')
            ->where('type', 'thana')
            ->whereNull('parent_id')
            ->where('station_id', '!=', $hqId)
            ->update(['parent_id' => $hqId]);

        DB::statement("UPDATE users u
            JOIN officers o ON o.user_id = u.id
            SET u.station_id = o.station_id,
                u.officer_id = o.officer_id,
                u.role = CASE WHEN u.role = 'officer' THEN 'station_oc' ELSE u.role END
            WHERE o.is_oc = 1");

        DB::table('users')->where('role', 'officer')->update(['role' => 'station_oc']);

        DB::table('users')
            ->where('role', 'super_admin')
            ->whereNull('station_id')
            ->update(['station_id' => $hqId]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','metro_head','district_head','station_oc','citizen') NOT NULL DEFAULT 'citizen'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','officer','metro_head','district_head','station_oc','citizen') NOT NULL DEFAULT 'citizen'");
            DB::table('users')->where('role', 'station_oc')->update(['role' => 'officer']);
            DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','officer','citizen') NOT NULL DEFAULT 'citizen'");
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'officer_id')) {
                $table->dropForeign(['officer_id']);
                $table->dropColumn('officer_id');
            }

            if (Schema::hasColumn('users', 'station_id')) {
                $table->dropForeign(['station_id']);
                $table->dropColumn('station_id');
            }
        });

        Schema::table('stations', function (Blueprint $table) {
            if (Schema::hasColumn('stations', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }

            foreach (['type', 'division', 'head_rank', 'jurisdiction', 'is_active'] as $column) {
                if (Schema::hasColumn('stations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE stations SET district = '' WHERE district IS NULL");
            DB::statement('ALTER TABLE stations MODIFY district VARCHAR(50) NOT NULL');
        }
    }
};
