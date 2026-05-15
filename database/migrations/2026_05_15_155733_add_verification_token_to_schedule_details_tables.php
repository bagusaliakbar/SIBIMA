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
        Schema::table('seminar_schedule_details', function (Blueprint $table) {
            $table->string('verification_token')->nullable()->unique()->after('id');
        });

        Schema::table('thesis_defense_schedule_details', function (Blueprint $table) {
            $table->string('verification_token')->nullable()->unique()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seminar_schedule_details', function (Blueprint $table) {
            $table->dropColumn('verification_token');
        });

        Schema::table('thesis_defense_schedule_details', function (Blueprint $table) {
            $table->dropColumn('verification_token');
        });
    }
};
