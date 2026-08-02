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
        \Illuminate\Support\Facades\DB::table('users')
            ->where('max_quota', 8)
            ->update(['max_quota' => 10]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration
    }
};
