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
    Schema::create('officers', function (Blueprint $table) {
        $table->id('officer_id'); // Primary Key
        
        // Foreign Key linking to stations
        $table->unsignedBigInteger('station_id')->nullable(); 
        
        $table->string('name', 100);
        $table->string('badge_number', 20)->unique();
        $table->string('rank', 50);
        $table->string('status', 20)->default('Active');
        $table->timestamps();

        // Defining the relationship: If a station is deleted, set the officer's station_id to NULL
        $table->foreign('station_id')
              ->references('station_id')
              ->on('stations')
              ->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officers');
    }
};
