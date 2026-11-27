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
        Schema::table('thesis_defense_applications', function (Blueprint $table) {
            if (Schema::hasColumn('thesis_defense_applications', 'file_formulir')) {
                $table->string('file_formulir')->nullable()->change();
            }
            if (!Schema::hasColumn('thesis_defense_applications', 'form_data')) {
                $table->json('form_data')->nullable()->after('file_formulir');
            }
            if (!Schema::hasColumn('thesis_defense_applications', 'statement_agreed_at')) {
                $table->timestamp('statement_agreed_at')->nullable()->after('form_data');
            }
            if (!Schema::hasColumn('thesis_defense_applications', 'verification_token')) {
                $table->string('verification_token', 64)->nullable()->unique()->after('statement_agreed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thesis_defense_applications', function (Blueprint $table) {
            $columns = array_values(array_filter(
                ['form_data', 'statement_agreed_at', 'verification_token'],
                fn ($col) => Schema::hasColumn('thesis_defense_applications', $col)
            ));
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
