<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citizen_complaints', function (Blueprint $table) {
            $table->string('complaint_title', 150)->nullable()->after('complainant_nid');
        });

        DB::table('citizen_complaints')
            ->whereNull('complaint_title')
            ->orderBy('complaint_id')
            ->chunkById(100, function ($complaints): void {
                foreach ($complaints as $complaint) {
                    $description = (string) $complaint->description;
                    $title = mb_strlen($description) > 120
                        ? mb_substr($description, 0, 117).'...'
                        : $description;

                    DB::table('citizen_complaints')
                        ->where('complaint_id', $complaint->complaint_id)
                        ->update(['complaint_title' => $title]);
                }
            }, 'complaint_id');

        Schema::table('citizen_complaints', function (Blueprint $table) {
            $table->string('complaint_title', 150)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('citizen_complaints', function (Blueprint $table) {
            $table->dropColumn('complaint_title');
        });
    }
};
