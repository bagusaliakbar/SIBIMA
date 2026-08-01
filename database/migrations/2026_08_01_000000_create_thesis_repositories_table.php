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
        Schema::create('thesis_repositories', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->nullable(); // NPM
            $table->string('name'); // Student Name
            $table->integer('year'); // Graduation Year
            $table->string('title'); // Thesis Title
            $table->text('abstract')->nullable();
            $table->string('pembimbing1')->nullable();
            $table->string('pembimbing2')->nullable();
            $table->string('file_path')->nullable(); // Optional PDF
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thesis_repositories');
    }
};
