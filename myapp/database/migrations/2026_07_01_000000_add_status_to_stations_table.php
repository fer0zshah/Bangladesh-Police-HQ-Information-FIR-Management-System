<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('stations', 'status')) {
            Schema::table('stations', function (Blueprint $table) {
                $table->string('status', 20)->default('Active')->after('contact_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('stations', 'status')) {
            Schema::table('stations', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
