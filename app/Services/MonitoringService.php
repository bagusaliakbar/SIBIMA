<?php

namespace App\Services;

use App\Models\ThesisDefenseScheduleDetail;
use App\Models\Wave;
use App\Models\User;

class MonitoringService
{
    /**
     * Get the active wave or default.
     */
    public function getActiveWave($selectedWaveId = null)
    {
        $activeWave = Wave::getCurrentActive();
        $selectedWaveId = $selectedWaveId ?: $activeWave?->id;

        return [$activeWave, $selectedWaveId];
    }

    /**
     * Calculate defense scores and grade.
     */
    public function calculateDefenseScores(ThesisDefenseScheduleDetail $detail)
    {
        $detail->load(['thesis.pembimbing1', 'thesis.pembimbing2', 'revisions']);
        
        $revP1 = $detail->revisions->where('examiner_id', $detail->thesis->pembimbing1_id)->first();
        $revE1 = $detail->revisions->where('examiner_id', $detail->examiner1_id)->first();
        $revE2 = $detail->revisions->where('examiner_id', $detail->examiner2_id)->first();

        $calc = function($rev) {
            if (!$rev || $rev->score_presentation === null) return null;
            return ($rev->score_presentation * 0.25) + ($rev->score_explanation * 0.40) + ($rev->score_writing * 0.35);
        };

        $scoreP1 = $calc($revP1);
        $scoreE1 = $calc($revE1);
        $scoreE2 = $calc($revE2);

        $scores = collect([$scoreP1, $scoreE1, $scoreE2])->filter(fn($s) => $s !== null);
        $totalScore = $scores->sum();
        $finalScore = $scores->count() > 0 ? $totalScore / $scores->count() : 0;

        $finalGrade = $scores->count() > 0 ? $this->getGrade($finalScore) : '-';

        // Averages for presentation, explanation, writing
        $pres_scores = collect([$revP1->score_presentation ?? null, $revE1->score_presentation ?? null, $revE2->score_presentation ?? null])->filter(fn($s) => $s !== null);
        $avgPres = $pres_scores->count() > 0 ? $pres_scores->avg() : 0;
        
        $expl_scores = collect([$revP1->score_explanation ?? null, $revE1->score_explanation ?? null, $revE2->score_explanation ?? null])->filter(fn($s) => $s !== null);
        $avgExpl = $expl_scores->count() > 0 ? $expl_scores->avg() : 0;
        
        $writ_scores = collect([$revP1->score_writing ?? null, $revE1->score_writing ?? null, $revE2->score_writing ?? null])->filter(fn($s) => $s !== null);
        $avgWrit = $writ_scores->count() > 0 ? $writ_scores->avg() : 0;

        return compact(
            'revP1', 'revE1', 'revE2', 'scoreP1', 'scoreE1', 'scoreE2', 
            'avgPres', 'avgExpl', 'avgWrit', 'finalScore', 'finalGrade'
        );
    }

    /**
     * Calculate seminar scores and grade.
     */
    public function calculateSeminarScores(\App\Models\SeminarScheduleDetail $detail)
    {
        $detail->load(['thesis.pembimbing1', 'revisions']);
        
        $revE1 = $detail->revisions->where('examiner_id', $detail->examiner1_id)->first();
        $revE2 = $detail->revisions->where('examiner_id', $detail->examiner2_id)->first();

        $calc = function($rev) {
            if (!$rev || $rev->score_presentation === null) return null;
            return ($rev->score_presentation * 0.25) + ($rev->score_explanation * 0.40) + ($rev->score_writing * 0.35);
        };

        $scoreE1 = $calc($revE1);
        $scoreE2 = $calc($revE2);

        $scores = collect([$scoreE1, $scoreE2])->filter(fn($s) => $s !== null);
        $totalScore = $scores->sum();
        $finalScore = $scores->count() > 0 ? $totalScore / $scores->count() : 0;

        $finalGrade = $scores->count() > 0 ? $this->getGrade($finalScore) : '-';

        // Averages
        $pres_scores = collect([$revE1->score_presentation ?? null, $revE2->score_presentation ?? null])->filter(fn($s) => $s !== null);
        $avgPres = $pres_scores->count() > 0 ? $pres_scores->avg() : 0;
        
        $expl_scores = collect([$revE1->score_explanation ?? null, $revE2->score_explanation ?? null])->filter(fn($s) => $s !== null);
        $avgExpl = $expl_scores->count() > 0 ? $expl_scores->avg() : 0;
        
        $writ_scores = collect([$revE1->score_writing ?? null, $revE2->score_writing ?? null])->filter(fn($s) => $s !== null);
        $avgWrit = $writ_scores->count() > 0 ? $writ_scores->avg() : 0;

        return compact(
            'revE1', 'revE2', 'scoreE1', 'scoreE2', 
            'avgPres', 'avgExpl', 'avgWrit', 'finalScore', 'finalGrade'
        );
    }


    /**
     * Convert score to grade.
     */
    public function getGrade($score)
    {
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B';
        if ($score >= 60) return 'C';
        if ($score >= 50) return 'D';
        return 'E';
    }

    /**
     * Get critical students query.
     */
    public function getCriticalStudentsQuery($search = null)
    {
        return User::criticalSemester()
            ->whereHas('thesis', function($q) {
                $q->where('status', '!=', 'completed');
            })
            ->when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('identifier', 'like', "%{$search}%");
                });
            })
            ->with(['thesis.pembimbing1'])
            ->orderBy('entry_year', 'asc');
    }
}
