<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\HasActivityLog;
use Carbon\Carbon;

class Graduation extends Model
{
    use HasFactory, Auditable, HasActivityLog;

    protected $fillable = [
        'thesis_id',
        'student_id',
        'status',
        'rejection_reason',
        'final_thesis_file',
        'journal_article_file',
        'publication_link',
        'plagiarism_file',
        'student_notes',
        'hardcover_collected',
        'hardcover_collected_at',
        'library_clearance',
        'library_clearance_at',
        'lab_clearance',
        'lab_clearance_at',
        'cd_or_repository_collected',
        'cd_or_repository_collected_at',
        'skl_number',
        'graduation_date',
        'gpa',
        'predicate',
        'verification_token',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'hardcover_collected' => 'boolean',
        'library_clearance' => 'boolean',
        'lab_clearance' => 'boolean',
        'cd_or_repository_collected' => 'boolean',
        'hardcover_collected_at' => 'datetime',
        'library_clearance_at' => 'datetime',
        'lab_clearance_at' => 'datetime',
        'cd_or_repository_collected_at' => 'datetime',
        'graduation_date' => 'date',
        'approved_at' => 'datetime',
        'gpa' => 'float',
    ];

    public function thesis()
    {
        return $this->belongsTo(Thesis::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if all clearances are completed.
     */
    public function isClearanceComplete(): bool
    {
        return $this->hardcover_collected && 
               $this->library_clearance && 
               $this->lab_clearance && 
               $this->cd_or_repository_collected;
    }

    /**
     * Get clearance completion percentage (0 - 100).
     */
    public function getClearanceProgressPercentageAttribute(): int
    {
        $total = 4;
        $done = 0;
        if ($this->hardcover_collected) $done++;
        if ($this->library_clearance) $done++;
        if ($this->lab_clearance) $done++;
        if ($this->cd_or_repository_collected) $done++;

        return (int) round(($done / $total) * 100);
    }

    /**
     * Formatted Indonesian graduation date.
     */
    public function getFormattedGraduationDateAttribute(): ?string
    {
        return $this->graduation_date 
            ? Carbon::parse($this->graduation_date)->locale('id')->translatedFormat('d F Y') 
            : null;
    }

    /**
     * Predicate recommendation based on GPA.
     */
    public static function determinePredicate(float $gpa): string
    {
        if ($gpa >= 3.75) {
            return 'Dengan Pujian (Cum Laude)';
        } elseif ($gpa >= 3.00) {
            return 'Sangat Memuaskan';
        } elseif ($gpa >= 2.75) {
            return 'Memuaskan';
        } else {
            return 'Cukup';
        }
    }
}
