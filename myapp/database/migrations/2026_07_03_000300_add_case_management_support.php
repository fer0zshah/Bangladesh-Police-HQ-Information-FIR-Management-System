<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_firs', function (Blueprint $table) {
            $table->unsignedBigInteger('complaint_id')->nullable()->change();
        });

        Schema::create('case_audit_logs', function (Blueprint $table) {
            $table->id('audit_log_id');
            $table->unsignedBigInteger('case_id');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 80);
            $table->string('old_status', 30)->nullable();
            $table->string('new_status', 30)->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
            $table->foreign('case_id')->references('case_id')->on('case_firs')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_audit_logs');
        Schema::table('case_firs', function (Blueprint $table) {
            $table->unsignedBigInteger('complaint_id')->nullable(false)->change();
        });
    }
};
