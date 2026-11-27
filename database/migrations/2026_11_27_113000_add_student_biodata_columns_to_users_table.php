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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'birth_place')) {
                $table->string('birth_place', 120)->nullable()->after('birth_date');
            }
            if (!Schema::hasColumn('users', 'study_program')) {
                $table->string('study_program', 120)->nullable()->after('entry_year');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone_number');
            }
            if (!Schema::hasColumn('users', 'postal_code')) {
                $table->string('postal_code', 10)->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'home_phone')) {
                $table->string('home_phone', 30)->nullable()->after('postal_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = array_values(array_filter(
                ['birth_place', 'study_program', 'address', 'postal_code', 'home_phone'],
                fn ($col) => Schema::hasColumn('users', $col)
            ));
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
