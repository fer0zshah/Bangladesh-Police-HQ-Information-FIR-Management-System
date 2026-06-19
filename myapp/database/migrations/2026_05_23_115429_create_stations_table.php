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
    Schema::create('stations', function (Blueprint $table) {
        $table->id('station_id'); // Primary Key
        $table->string('name', 100);
        $table->string('district', 50);
        $table->text('address')->nullable();
        $table->string('contact_number', 15)->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse  the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};
