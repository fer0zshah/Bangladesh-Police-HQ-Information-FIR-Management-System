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
        Schema::create('case_criminals', function (Blueprint $table) {
            $table->id('involvement_id');
            $table->unsignedBigInteger('case_id');
            $table->unsignedBigInteger('criminal_id');
            $table->string('involvement_type', 100);
            $table->timestamps();

            $table->foreign('case_id')
                ->references('case_id')
                ->on('case_firs')
                ->onDelete('cascade');

            $table->foreign('criminal_id')
                ->references('criminal_id')
                ->on('criminals')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_criminals');
    }
};
