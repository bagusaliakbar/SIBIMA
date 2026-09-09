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
        Schema::table('thesis_repositories', function (Blueprint $table) {
            $table->string('file_path_bab3')->nullable()->after('file_path_bab2');
            $table->string('file_path_bab4')->nullable()->after('file_path_bab3');
            $table->string('file_path_bab5')->nullable()->after('file_path_bab4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thesis_repositories', function (Blueprint $table) {
            $table->dropColumn(['file_path_bab3', 'file_path_bab4', 'file_path_bab5']);
        });
    }
};
