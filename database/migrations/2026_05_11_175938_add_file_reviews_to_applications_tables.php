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
        Schema::table('seminar_applications', function (Blueprint $table) {
            $table->json('file_reviews')->nullable();
        });

        Schema::table('thesis_defense_applications', function (Blueprint $table) {
            $table->json('file_reviews')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seminar_applications', function (Blueprint $table) {
            $table->dropColumn('file_reviews');
        });

        Schema::table('thesis_defense_applications', function (Blueprint $table) {
            $table->dropColumn('file_reviews');
        });
    }
};
