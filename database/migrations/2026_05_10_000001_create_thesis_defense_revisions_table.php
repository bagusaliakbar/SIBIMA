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
        Schema::create('thesis_defense_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_defense_schedule_detail_id')->constrained('thesis_defense_schedule_details', 'id', 'td_rev_schedule_detail_id')->onDelete('cascade');
            $table->foreignId('examiner_id')->constrained('users')->onDelete('cascade');
            $table->text('revision_notes')->nullable();
            $table->string('revision_file')->nullable();
            $table->text('student_notes')->nullable();
            $table->string('student_file')->nullable();
            $table->timestamp('resubmitted_at')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thesis_defense_revisions');
    }
};
