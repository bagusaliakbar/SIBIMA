<?php

namespace App\Services;

use App\Models\Thesis;

class ThesisAnalyticsService
{
    /**
     * Get topic distribution trends grouped by student cohort.
     *
     * @return array
     */
    public function getTopicTrends(): array
    {
        $theses = Thesis::whereNotNull('topic')
            ->with('student')
            ->get();

        $cohorts = $theses->map(fn($t) => $t->student?->entry_year)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $trends = [];

        foreach ($cohorts as $year) {
            $cohortTheses = $theses->filter(fn($t) => $t->student?->entry_year == $year);
            $totalInCohort = $cohortTheses->count();

            if ($totalInCohort > 0) {
                $trends["Angkatan $year"] = $cohortTheses->groupBy('topic')
                    ->map(fn($group) => round(($group->count() / $totalInCohort) * 100, 1));
            }
        }

        return $trends;
    }
}
