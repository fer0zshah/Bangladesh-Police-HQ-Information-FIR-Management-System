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
        Schema::create('evidence', function (Blueprint $table) {
            $table->id('evidence_id');
            $table->unsignedBigInteger('case_id');
            $table->unsignedBigInteger('officer_id');
            $table->string('type', 100);
            $table->text('description')->nullable();
            $table->date('collected_date');
            $table->timestamps();

            $table->foreign('case_id')
                ->references('case_id')
                ->on('case_firs')
                ->onDelete('cascade');

            $table->foreign('officer_id')
                ->references('officer_id')
                ->on('officers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidence');
    }
};
