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
        Schema::create('wa_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('target_type'); // e.g. mahasiswa_belum_seminar, mahasiswa_bimbingan_pasif, mahasiswa_kritis, etc.
            $table->json('target_filter')->nullable();
            $table->text('message_template');
            $table->integer('delay_seconds')->default(4);
            $table->integer('total_recipients')->default(0);
            $table->integer('successful_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->string('status')->default('completed'); // draft, processing, completed, cancelled
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('wa_broadcast_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wa_broadcast_id')->constrained('wa_broadcasts')->cascadeOnDelete();
            $table->foreignId('recipient_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipient_name');
            $table->string('recipient_identifier')->nullable(); // NPM / NIDN
            $table->string('recipient_phone')->nullable();
            $table->text('message_content');
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_broadcast_logs');
        Schema::dropIfExists('wa_broadcasts');
    }
};
