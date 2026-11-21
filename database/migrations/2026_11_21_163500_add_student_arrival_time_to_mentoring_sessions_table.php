<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mentoring_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('mentoring_sessions', 'student_arrival_time')) {
                $table->string('student_arrival_time', 5)->nullable()->after('student_attendance_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mentoring_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('mentoring_sessions', 'student_arrival_time')) {
                $table->dropColumn('student_arrival_time');
            }
        });
    }
};
