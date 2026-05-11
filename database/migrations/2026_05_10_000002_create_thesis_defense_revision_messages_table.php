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
        Schema::create('thesis_defense_revision_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_defense_revision_id')->constrained('thesis_defense_revisions', 'id', 'td_rev_msg_revision_id')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users');
            $table->text('message');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thesis_defense_revision_messages');
    }
};
