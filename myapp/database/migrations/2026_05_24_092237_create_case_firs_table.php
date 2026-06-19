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
        Schema::create('case_firs', function (Blueprint $table) {
            $table->id('case_id');
            $table->unsignedBigInteger('station_id');
            $table->unsignedBigInteger('investigating_officer_id');
            $table->unsignedBigInteger('complaint_id');
            $table->string('case_title');
            $table->date('date_filed');
            $table->string('status',20)->default('pending');
            $table->timestamps();

            $table->foreign('station_id')
            ->references('station_id')
            ->on('stations')
            ->onDelete('cascade');
             $table->foreign('investigating_officer_id')
            ->references('officer_id')
            ->on('officers')
            ->onDelete('cascade');
            $table->foreign('complaint_id')
            ->references('complaint_id')
            ->on('citizen_complaints')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_firs');
    }
};
