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
            $table->foreignId('wave_id')->nullable()->constrained('waves')->onDelete('set null');
        });

        Schema::table('thesis_defense_applications', function (Blueprint $table) {
            $table->foreignId('wave_id')->nullable()->constrained('waves')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seminar_applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('wave_id');
        });

        Schema::table('thesis_defense_applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('wave_id');
        });
    }
};
