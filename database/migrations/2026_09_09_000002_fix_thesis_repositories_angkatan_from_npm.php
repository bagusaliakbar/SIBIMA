<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\ThesisRepository;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengoreksi tahun angkatan katalog repositori naskah skripsi berdasarkan pola NPM (misal D1A17 => 2017).
     */
    public function up(): void
    {
        $records = DB::table('thesis_repositories')
            ->select('id', 'identifier', 'year')
            ->whereNotNull('identifier')
            ->get();

        foreach ($records as $record) {
            $extracted = ThesisRepository::extractYearFromIdentifier($record->identifier);
            if ($extracted && $extracted != $record->year) {
                DB::table('thesis_repositories')
                    ->where('id', $record->id)
                    ->update(['year' => $extracted]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data fix migration - no rollback needed
    }
};
