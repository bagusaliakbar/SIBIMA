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
        Schema::create('graduations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_id')->constrained('theses')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            
            // Status & Catatan Pengajuan
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            
            // Berkas Unggahan Mahasiswa
            $table->string('final_thesis_file')->nullable();
            $table->string('journal_article_file')->nullable();
            $table->string('publication_link')->nullable();
            $table->string('plagiarism_file')->nullable();
            $table->text('student_notes')->nullable();
            
            // Checklist Bebas Tanggungan (Clearance) oleh Admin / Kaprodi
            $table->boolean('hardcover_collected')->default(false);
            $table->timestamp('hardcover_collected_at')->nullable();
            $table->boolean('library_clearance')->default(false);
            $table->timestamp('library_clearance_at')->nullable();
            $table->boolean('lab_clearance')->default(false);
            $table->timestamp('lab_clearance_at')->nullable();
            $table->boolean('cd_or_repository_collected')->default(false);
            $table->timestamp('cd_or_repository_collected_at')->nullable();
            
            // Data Penerbitan SKL (Surat Keterangan Lulus)
            $table->string('skl_number')->nullable();
            $table->date('graduation_date')->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->string('predicate')->nullable(); // e.g. Dengan Pujian (Cum Laude), Sangat Memuaskan, Memuaskan
            $table->string('verification_token', 64)->unique()->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('graduations');
    }
};
