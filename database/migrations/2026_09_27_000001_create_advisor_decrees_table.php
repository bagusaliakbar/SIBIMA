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
        Schema::create('advisor_decrees', function (Blueprint $table) {
            $table->id();
            $table->string('decree_number');
            $table->string('title')->default('Penetapan Dosen Pembimbing Skripsi Mahasiswa');
            $table->string('academic_year')->default('2025/2026');
            $table->string('semester')->default('Ganjil'); // Ganjil / Genap
            $table->string('target_type')->default('collective'); // collective / individual_dosen
            $table->foreignId('dosen_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('wave_id')->nullable()->constrained('waves')->nullOnDelete();
            $table->date('decree_date');
            $table->string('signatory_title')->default('Dekan Fakultas Ilmu Komputer');
            $table->string('signatory_name');
            $table->string('signatory_identifier')->nullable(); // NIDN / NIP
            $table->foreignId('signer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('theses_data'); // Snapshot of assigned theses
            $table->unsignedInteger('total_students')->default(0);
            $table->string('verification_token', 64)->unique();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['decree_number', 'decree_date']);
            $table->index(['academic_year', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advisor_decrees');
    }
};
