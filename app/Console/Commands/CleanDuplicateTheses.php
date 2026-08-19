<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Thesis;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CleanDuplicateTheses extends Command
{
    protected $signature = 'app:clean-duplicate-theses {--dry-run : Only show duplicates without deleting}';
    protected $description = 'Clean up duplicate thesis submissions for students';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info("Scanning for duplicate thesis records in database...");

        $duplicateGroups = Thesis::select('student_id', DB::raw('count(*) as total'))
            ->groupBy('student_id')
            ->having('total', '>', 1)
            ->get();

        if ($duplicateGroups->isEmpty()) {
            $this->info("✓ No duplicate theses found! All students have at most 1 thesis.");
            return 0;
        }

        $this->warn("Found " . $duplicateGroups->count() . " student(s) with duplicate thesis submissions:\n");

        $deletedCount = 0;

        foreach ($duplicateGroups as $group) {
            $student = User::find($group->student_id);
            $studentName = $student ? "{$student->name} ({$student->identifier})" : "Student ID {$group->student_id}";

            $this->line("<fg=yellow>► Student: {$studentName}</>");

            $theses = Thesis::where('student_id', $group->student_id)
                ->orderBy('created_at', 'asc')
                ->get();

            // Keep the first (primary) thesis, remove subsequent duplicates
            $primary = $theses->first();
            $this->line("  [KEEP] Primary Thesis ID #{$primary->id}: '{$primary->title}' (Created: {$primary->created_at})");

            $duplicates = $theses->slice(1);
            foreach ($duplicates as $dup) {
                if ($isDryRun) {
                    $this->line("  <fg=red>[WOULD DELETE]</> Duplicate Thesis ID #{$dup->id}: '{$dup->title}' (Created: {$dup->created_at})");
                } else {
                    $dup->delete();
                    $this->line("  <fg=green>[DELETED]</> Duplicate Thesis ID #{$dup->id}: '{$dup->title}'");
                    $deletedCount++;
                }
            }
            $this->line("");
        }

        if (!$isDryRun) {
            Cache::flush();
            $this->info("✓ Successfully removed {$deletedCount} duplicate thesis record(s) and cleared similarity cache!");
        } else {
            $this->info("Dry-run completed. Re-run without --dry-run to delete.");
        }

        return 0;
    }
}
