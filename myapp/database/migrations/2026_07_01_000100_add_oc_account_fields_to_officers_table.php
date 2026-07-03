<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('officers', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->unique()
                ->after('officer_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->boolean('is_oc')->default(false)->index()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('officers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('is_oc');
        });
    }
};
