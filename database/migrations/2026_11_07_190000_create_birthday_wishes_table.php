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
        if (!Schema::hasTable('birthday_wishes')) {
            Schema::create('birthday_wishes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
                $table->unsignedSmallInteger('year');
                $table->string('message', 300);
                $table->timestamps();

                $table->unique(['from_user_id', 'to_user_id', 'year'], 'uniq_birthday_wish_per_year');
                $table->index(['to_user_id', 'year']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('birthday_wishes');
    }
};

