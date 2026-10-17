<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('thesis_defense_revision_messages') && !Schema::hasColumn('thesis_defense_revision_messages', 'read_at')) {
            Schema::table('thesis_defense_revision_messages', function (Blueprint $table) {
                $table->timestamp('read_at')->nullable()->after('file_path');
            });
        }

        if (Schema::hasTable('seminar_revision_messages') && !Schema::hasColumn('seminar_revision_messages', 'read_at')) {
            Schema::table('seminar_revision_messages', function (Blueprint $table) {
                $table->timestamp('read_at')->nullable()->after('file_path');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('thesis_defense_revision_messages') && Schema::hasColumn('thesis_defense_revision_messages', 'read_at')) {
            Schema::table('thesis_defense_revision_messages', function (Blueprint $table) {
                $table->dropColumn('read_at');
            });
        }

        if (Schema::hasTable('seminar_revision_messages') && Schema::hasColumn('seminar_revision_messages', 'read_at')) {
            Schema::table('seminar_revision_messages', function (Blueprint $table) {
                $table->dropColumn('read_at');
            });
        }
    }
};
