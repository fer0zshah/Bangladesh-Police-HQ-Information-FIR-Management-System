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
            DB::statement("ALTER TABLE users MODIFY role ENUM(
                'super_admin','metro_head','district_head','station_oc','citizen'
            ) NOT NULL DEFAULT 'citizen'");
        } else {
            Schema::table('stations', function (Blueprint $table) {
                $table->string('district', 50)->nullable()->change();
            });
        }

        Schema::table('stations', function (Blueprint $table) {
            $table->string('type', 30)->default('thana')->after('name');
            $table->foreignId('parent_id')
                ->nullable()
                ->after('type')
                ->constrained('stations', 'station_id')
                ->nullOnDelete();
            $table->string('division', 50)->nullable()->after('district');
            $table->string('head_rank', 80)->default('OC')->after('division');
            $table->string('jurisdiction')->nullable()->after('contact_number');
            $table->boolean('is_active')->default(true)->after('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('station_id')
                ->nullable()
                ->after('role')
                ->constrained('stations', 'station_id')
                ->nullOnDelete();
            $table->foreignId('officer_id')
                ->nullable()
                ->after('station_id')
                ->constrained('officers', 'officer_id')
                ->nullOnDelete();
        });

        DB::table('stations')->update([
            'type' => 'thana',
            'head_rank' => 'OC',
        ]);

        DB::statement("UPDATE stations SET is_active = CASE WHEN LOWER(status) = 'active' THEN 1 ELSE 0 END");

        $hqId = DB::table('stations')
            ->where('name', 'Bangladesh Police Headquarters')
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
            ]);
        }

        DB::table('stations')
            ->where('type', 'thana')
            ->whereNull('parent_id')
            ->where('station_id', '<>', $hqId)
            ->update(['parent_id' => $hqId]);

        DB::table('users')
            ->where('role', 'super_admin')
            ->whereNull('station_id')
            ->update(['station_id' => $hqId]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('officer_id');
            $table->dropConstrainedForeignId('station_id');
        });

        Schema::table('stations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
            $table->dropColumn(['type', 'division', 'head_rank', 'jurisdiction', 'is_active']);
        });

        DB::statement("UPDATE stations SET district = 'Unknown' WHERE district IS NULL");

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM(
                'super_admin','station_oc','citizen'
            ) NOT NULL DEFAULT 'citizen'");
            DB::statement('ALTER TABLE stations MODIFY district VARCHAR(50) NOT NULL');
        } else {
            Schema::table('stations', function (Blueprint $table) {
                $table->string('district', 50)->nullable(false)->change();
            });
        }
    }
};
