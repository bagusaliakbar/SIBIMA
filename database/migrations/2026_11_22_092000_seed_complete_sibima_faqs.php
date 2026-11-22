<?php

use Database\Seeders\FaqSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('faqs')) {
            (new FaqSeeder())->run();
        }
    }

    public function down(): void
    {
        // Intentionally non-destructive: preserve admin-managed FAQ entries on rollback.
    }
};
