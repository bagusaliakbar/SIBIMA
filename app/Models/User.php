<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'identifier',
        'is_active',
        'avatar',
        'entry_year',
        'phone_number',
        'signature',
        'signature_token',
    ];

    /**
     * Get signature URL
     */
    public function getSignatureUrlAttribute()
    {
        return $this->signature ? asset('storage/' . $this->signature) : null;
    }

    /**
     * Get current semester based on entry year
     */
    public function getCurrentSemesterAttribute()
    {
        if (!$this->entry_year) return null;

        $now = now();
        $yearDiff = $now->year - $this->entry_year;
        
        // In Indonesia, odd semester (ganjil) starts around July/August
        // even semester (genap) starts around January/February
        $month = $now->month;
        
        if ($month >= 7) {
            return ($yearDiff * 2) + 1;
        } else {
            return $yearDiff * 2;
        }
    }

    public function getIsCriticalSemesterAttribute()
    {
        $sem = $this->current_semester;
        return $sem !== null && $sem >= 13;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the messages sent by the user.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the messages received by the user.
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get the thesis associated with the student.
     */
    public function thesis()
    {
        return $this->hasOne(Thesis::class, 'student_id');
    }

    /**
     * Get the theses where this user is Pembimbing 1.
     */
    public function thesesAsP1()
    {
        return $this->hasMany(Thesis::class, 'pembimbing1_id');
    }

    /**
     * Get the theses where this user is Pembimbing 2.
     */
    public function thesesAsP2()
    {
        return $this->hasMany(Thesis::class, 'pembimbing2_id');
    }

    /**
     * Get the URL for the user's avatar.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Fallback to initials-based SVG if no avatar is uploaded
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&color=FFFFFF&background=f97316&bold=true";
    }

    public function scopeCriticalSemester($query)
    {
        $currentYear = now()->year;
        $isSecondHalf = now()->month >= 7;
        $thresholdYear = $isSecondHalf ? ($currentYear - 6) : ($currentYear - 7);

        return $query->where('role', 'mahasiswa')
            ->whereNotNull('entry_year')
            ->where('entry_year', '<=', $thresholdYear);
    }
}
