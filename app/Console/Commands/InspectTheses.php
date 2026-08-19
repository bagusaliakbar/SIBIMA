<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Thesis;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InspectTheses extends Command
{
    protected $signature = 'app:inspect-theses';
    protected $description = 'Inspect duplicate theses';

    public function handle()
    {
        $theses = Thesis::with('student')
            ->whereHas('student', function($q) {
                $q->where('identifier', 'D1A230100')
                  ->orWhere('name', 'like', '%RANGGA SANDI SAPUTRA%');
            })
            ->get();

        $this->info("Found " . $theses->count() . " theses for Rangga Sandi Saputra:");
        foreach ($theses as $t) {
            $this->line("ID: {$t->id} | Student ID: {$t->student_id} | Status: {$t->status} | Created: {$t->created_at} | Title: {$t->title}");
        }

        $allDups = Thesis::select('student_id', DB::raw('count(*) as count'))
            ->groupBy('student_id')
            ->having('count', '>', 1)
            ->get();

        $this->warn("\nAll students with > 1 thesis in DB (" . $allDups->count() . " students):");
        foreach ($allDups as $d) {
            $u = User::find($d->student_id);
            $this->line("Student ID: {$d->student_id} | NPM: " . ($u->identifier ?? '-') . " | Name: " . ($u->name ?? '-') . " | Count: {$d->count}");
        }
    }
}
