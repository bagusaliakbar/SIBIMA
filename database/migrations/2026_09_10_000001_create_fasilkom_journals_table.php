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
        Schema::create('fasilkom_journals', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique(); // e.g. oai:ojs2.ejournal.unsub.ac.id:article/415
            $table->unsignedBigInteger('article_id')->nullable()->index(); // e.g. 415
            $table->text('title');
            $table->json('authors')->nullable(); // JSON array of authors
            $table->string('authors_string', 1000)->nullable();
            $table->longText('abstract')->nullable();
            $table->json('subjects')->nullable(); // keywords/subjects
            $table->string('publication_date')->nullable();
            $table->integer('year')->nullable()->index();
            $table->string('volume')->nullable();
            $table->string('issue')->nullable();
            $table->string('pages')->nullable();
            $table->string('landing_page_url');
            $table->string('pdf_url')->nullable();
            $table->string('doi')->nullable();
            $table->string('publisher')->nullable();
            $table->string('issn')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fasilkom_journals');
    }
};
