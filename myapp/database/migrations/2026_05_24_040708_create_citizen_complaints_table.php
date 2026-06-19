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
        Schema::create('citizen_complaints', function (Blueprint $table) {
            $table->id('complaint_id');
            $table->unsignedBigInteger('station_id');
            $table->string('complainant_name',100);
            $table->string('complainant_nid', 20);
            $table->string('description');
            $table->date('submitted_date');
            $table->string('status', 20)->default('Pending');
            $table->timestamps();

            $table->foreign('station_id')
            ->references('station_id')
            ->on('stations')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citizen_complaints');
    }
};
