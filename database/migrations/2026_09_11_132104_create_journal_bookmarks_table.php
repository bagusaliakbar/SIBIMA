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
        Schema::create('journal_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('journal_identifier', 255);
            $table->text('title');
            $table->json('authors')->nullable();
            $table->text('authors_string')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('venue', 255)->nullable();
            $table->string('publisher', 255)->nullable();
            $table->string('doi', 255)->nullable();
            $table->text('url')->nullable();
            $table->text('pdf_url')->nullable();
            $table->longText('abstract')->nullable();
            $table->string('source', 50)->default('unknown');
            $table->string('source_label', 100)->nullable();
            $table->json('citations')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'journal_identifier'], 'user_journal_unique');
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_bookmarks');
    }
};
