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
        Schema::create('criminals', function (Blueprint $table) {
            $table->id('criminal_id');
            $table->string('nid_number',20)->unique()->nullable();
            $table->string('name',100);
            $table->string ('alias',100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->boolean('wanted_status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criminals');
    }
};
