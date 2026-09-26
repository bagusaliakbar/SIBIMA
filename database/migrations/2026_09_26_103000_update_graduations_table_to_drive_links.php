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
        Schema::table('graduations', function (Blueprint $table) {
            $table->text('final_thesis_file')->nullable()->change();
            $table->text('journal_article_file')->nullable()->change();
            $table->text('plagiarism_file')->nullable()->change();
            $table->text('publication_link')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('graduations', function (Blueprint $table) {
            $table->string('final_thesis_file', 255)->nullable()->change();
            $table->string('journal_article_file', 255)->nullable()->change();
            $table->string('plagiarism_file', 255)->nullable()->change();
            $table->string('publication_link', 255)->nullable()->change();
        });
    }
};
