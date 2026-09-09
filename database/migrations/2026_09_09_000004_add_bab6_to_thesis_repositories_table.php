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
            if (!Schema::hasColumn('thesis_repositories', 'file_path_bab6')) {
                $table->string('file_path_bab6', 1000)->nullable()->after('file_path_bab5');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thesis_repositories', function (Blueprint $table) {
            if (Schema::hasColumn('thesis_repositories', 'file_path_bab6')) {
                $table->dropColumn('file_path_bab6');
            }
        });
    }
};
