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
        Schema::create('letter_settings', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // surat_tugas_seminar, sk_penguji_sidang
            $table->string('format'); // e.g. [NUMBER]/UNSUB/FIK/[MONTH]/[YEAR]
            $table->integer('last_number')->default(0);
            $table->string('title')->nullable(); // For UI display
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_settings');
    }
};
