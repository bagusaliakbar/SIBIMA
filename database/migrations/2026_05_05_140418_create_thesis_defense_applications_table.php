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
        Schema::create('thesis_defense_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_id')->constrained()->onDelete('cascade');
            $table->string('file_formulir');
            $table->string('file_transkrip');
            $table->string('file_acc_pembimbing');
            $table->string('file_logbook');
            $table->string('file_pembayaran');
            $table->string('file_skripsi');
            $table->string('file_ktm');
            $table->string('file_pkkmb_univ');
            $table->string('file_pkkmb_fak');
            $table->string('file_makrab');
            $table->string('file_cisco');
            $table->string('file_workshop');
            $table->string('file_organisasi');
            $table->string('file_toefl');
            $table->string('file_kewirausahaan');
            $table->string('file_tahsin');
            $table->string('file_komputer');
            $table->string('file_perpus_pinjam');
            $table->string('file_perpus_sumbang');
            $table->string('file_ijazah');
            $table->string('status')->default('pending');
            $table->text('admin_feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thesis_defense_applications');
    }
};
