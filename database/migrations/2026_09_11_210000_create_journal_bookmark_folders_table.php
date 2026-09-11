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
        Schema::create('journal_bookmark_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('color')->default('orange');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'name']);
        });

        Schema::table('journal_bookmarks', function (Blueprint $table) {
            $table->foreignId('folder_id')->nullable()->after('user_id')
                ->constrained('journal_bookmark_folders')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_bookmarks', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
            $table->dropColumn('folder_id');
        });

        Schema::dropIfExists('journal_bookmark_folders');
    }
};
