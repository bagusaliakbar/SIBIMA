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
        Schema::table('thesis_defense_revisions', function (Blueprint $table) {
            $table->integer('score_presentation')->nullable()->after('status');
            $table->integer('score_explanation')->nullable()->after('score_presentation');
            $table->integer('score_writing')->nullable()->after('score_explanation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thesis_defense_revisions', function (Blueprint $table) {
            $table->dropColumn(['score_presentation', 'score_explanation', 'score_writing']);
        });
    }
};
