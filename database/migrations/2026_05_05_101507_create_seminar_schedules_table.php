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
        Schema::create('seminar_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g. "SEMESTER GENAP GELOMBANG 2 TAHUN AKADEMIK 2025/2026"
            $table->date('date');
            $table->foreignId('chairman_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('moderator_id')->constrained('users')->onDelete('cascade');
            $table->string('location')->nullable(); // e.g. "Online = Channel 2"
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seminar_schedules');
    }
};
