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
        Schema::table('mentoring_sessions', function (Blueprint $table) {
            $table->index(['thesis_id', 'status'], 'idx_ms_thesis_status');
            $table->index(['dosen_id', 'scheduled_at'], 'idx_ms_dosen_scheduled');
            $table->index(['status', 'scheduled_at'], 'idx_ms_status_scheduled');
            $table->index('student_attendance_status', 'idx_ms_attendance_status');
        });

        Schema::table('theses', function (Blueprint $table) {
            $table->index(['status', 'student_id'], 'idx_theses_status_student');
            $table->index(['status', 'pembimbing1_id'], 'idx_theses_status_p1');
            $table->index(['status', 'pembimbing2_id'], 'idx_theses_status_p2');
        });

        Schema::table('seminar_applications', function (Blueprint $table) {
            $table->index(['thesis_id', 'status'], 'idx_sem_thesis_status');
        });

        Schema::table('thesis_defense_applications', function (Blueprint $table) {
            $table->index(['thesis_id', 'status'], 'idx_def_thesis_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mentoring_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_ms_thesis_status');
            $table->dropIndex('idx_ms_dosen_scheduled');
            $table->dropIndex('idx_ms_status_scheduled');
            $table->dropIndex('idx_ms_attendance_status');
        });

        Schema::table('theses', function (Blueprint $table) {
            $table->dropIndex('idx_theses_status_student');
            $table->dropIndex('idx_theses_status_p1');
            $table->dropIndex('idx_theses_status_p2');
        });

        Schema::table('seminar_applications', function (Blueprint $table) {
            $table->dropIndex('idx_sem_thesis_status');
        });

        Schema::table('thesis_defense_applications', function (Blueprint $table) {
            $table->dropIndex('idx_def_thesis_status');
        });
    }
};
