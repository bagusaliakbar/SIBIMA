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
        Schema::create('seminar_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seminar_schedule_detail_id')->constrained('seminar_schedule_details')->onDelete('cascade');
            $table->foreignId('examiner_id')->constrained('users')->onDelete('cascade');
            $table->text('revision_notes')->nullable();
            $table->string('revision_file')->nullable();
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seminar_revisions');
    }
};
