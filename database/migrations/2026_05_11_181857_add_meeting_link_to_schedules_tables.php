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
        Schema::table('seminar_schedules', function (Blueprint $table) {
            $table->string('meeting_link')->nullable();
        });

        Schema::table('thesis_defense_schedules', function (Blueprint $table) {
            $table->string('meeting_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seminar_schedules', function (Blueprint $table) {
            $table->dropColumn('meeting_link');
        });

        Schema::table('thesis_defense_schedules', function (Blueprint $table) {
            $table->dropColumn('meeting_link');
        });
    }
};
