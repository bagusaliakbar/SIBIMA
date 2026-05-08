<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->boolean('acc_up_p1')->default(false)->after('status');
            $table->boolean('acc_up_p2')->default(false)->after('acc_up_p1');
            $table->boolean('acc_sidang_p1')->default(false)->after('acc_up_p2');
            $table->boolean('acc_sidang_p2')->default(false)->after('acc_sidang_p1');
        });
    }

    public function down(): void
    {
        Schema::table('theses', function (Blueprint $table) {
            $table->dropColumn(['acc_up_p1', 'acc_up_p2', 'acc_sidang_p1', 'acc_sidang_p2']);
        });
    }
};
