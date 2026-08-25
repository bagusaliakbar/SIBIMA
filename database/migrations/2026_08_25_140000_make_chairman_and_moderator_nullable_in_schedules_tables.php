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
            $table->unsignedBigInteger('chairman_id')->nullable()->change();
            $table->unsignedBigInteger('moderator_id')->nullable()->change();
        });

        Schema::table('thesis_defense_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('chairman_id')->nullable()->change();
            $table->unsignedBigInteger('moderator_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seminar_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('chairman_id')->nullable(false)->change();
            $table->unsignedBigInteger('moderator_id')->nullable(false)->change();
        });

        Schema::table('thesis_defense_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('chairman_id')->nullable(false)->change();
            $table->unsignedBigInteger('moderator_id')->nullable(false)->change();
        });
    }
};
