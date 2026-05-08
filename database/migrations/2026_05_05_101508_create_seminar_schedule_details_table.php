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
        Schema::create('seminar_schedule_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seminar_schedule_id')->constrained('seminar_schedules')->onDelete('cascade');
            $table->foreignId('thesis_id')->nullable()->constrained('theses')->onDelete('cascade');
            $table->string('activity_name')->nullable(); // For generic entries like "Persiapan"
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('examiner1_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('examiner2_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seminar_schedule_details');
    }
};
