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
        Schema::table('seminar_revisions', function (Blueprint $table) {
            $table->text('student_notes')->nullable()->after('revision_file');
            $table->string('student_file')->nullable()->after('student_notes');
            $table->timestamp('resubmitted_at')->nullable()->after('student_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seminar_revisions', function (Blueprint $table) {
            $table->dropColumn(['student_notes', 'student_file', 'resubmitted_at']);
        });
    }
};
