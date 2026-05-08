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
        Schema::table('theses', function (Blueprint $table) {
            $table->foreignId('requested_pembimbing1_id')->nullable()->after('pembimbing2_id')->constrained('users')->onDelete('set null');
            $table->foreignId('requested_pembimbing2_id')->nullable()->after('requested_pembimbing1_id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropForeign(['requested_pembimbing1_id']);
            $table->dropForeign(['requested_pembimbing2_id']);
            $table->dropColumn(['requested_pembimbing1_id', 'requested_pembimbing2_id']);
        });
    }
};
