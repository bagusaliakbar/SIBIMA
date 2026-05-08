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
        Schema::create('theses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pembimbing1_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('pembimbing2_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('abstract')->nullable();
            $table->enum('status', ['pending', 'active', 'completed', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theses');
    }
};
