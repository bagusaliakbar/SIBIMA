<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mentoring_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('mentoring_sessions', 'scheduled_end_at')) {
                $table->dateTime('scheduled_end_at')->nullable()->after('scheduled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mentoring_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('mentoring_sessions', 'scheduled_end_at')) {
                $table->dropColumn('scheduled_end_at');
            }
        });
    }
};
