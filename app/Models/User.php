<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Traits\Auditable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'identifier',
        'is_active',
        'avatar',
        'entry_year',
        'phone_number',
        'signature',
        'signature_token',
        'research_interests',
        'max_quota',
    ];

    /**
     * Get signature URL
     */
    public function getSignatureUrlAttribute()
    {
        if ($this->signature) {
            // For encrypted signatures, we might need a dedicated route to serve them securely if we want to display them via img src directly.
            // However, inline base64 is safer for PDFs and profile views.
            return null; // Deprecated: use decrypted_signature for inline base64 instead
        }
        return null;
    }

    /**
     * Get decrypted signature content
     */
    public function getDecryptedSignatureAttribute()
    {
        if ($this->signature) {
            // Check if it's an old file path
            if (str_starts_with($this->signature, 'signatures/')) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->signature)) {
                    try {
                        $rawBytes = Crypt::decrypt(\Illuminate\Support\Facades\Storage::disk('public')->get($this->signature));
                        return 'data:image/png;base64,' . base64_encode($rawBytes);
                    } catch (\Exception $e) {
                        return null;
                    }
                }
                return null;
            }

            // Otherwise, it's a direct DB string
            try {
                $decrypted = Crypt::decrypt($this->signature);
                // If for some reason it's raw bytes (legacy migration), wrap it
                if (!str_starts_with($decrypted, 'data:image')) {
                    return 'data:image/png;base64,' . base64_encode($decrypted);
                }
                return $decrypted;
            } catch (\Exception $e) {
                // If not encrypted (raw base64 string)
                if (str_starts_with($this->signature, 'data:image')) {
                    return $this->signature;
                }
                return null;
            }
        }
        return null;
    }

    public function getCurrentSemesterAttribute()
    {
        if (!$this->entry_year) return null;

        $now = now();
        $yearDiff = $now->year - $this->entry_year;
        
        // Di banyak kampus Indonesia, tahun ajaran baru (Ganjil) dimulai bulan September
        // sehingga Agustus masih masuk hitungan semester Genap sebelumnya.
        $month = $now->month;
        
        if ($month >= 9) {
            return ($yearDiff * 2) + 1;
        } else {
            return max(1, $yearDiff * 2);
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
            'phone_number' => 'encrypted',
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
            // Check if it's already a base64 string
            if (str_starts_with($this->avatar, 'data:image')) {
                return $this->avatar;
            }
            // Fallback for legacy files (just in case they weren't wiped yet)
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->avatar);
        }

        // Fallback to a default user silhouette SVG
        return 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect width="100%" height="100%" fill="%23f1f5f9"/><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="%2394a3b8"/></svg>';
    }

    public function scopeCriticalSemester($query)
    {
        $currentYear = now()->year;
        // Penentuan transisi semester kritis mengikuti bulan September (9)
        $isSecondHalf = now()->month >= 9;
        $thresholdYear = $isSecondHalf ? ($currentYear - 6) : ($currentYear - 7);

        return $query->where('role', 'mahasiswa')
            ->whereNotNull('entry_year')
            ->where('entry_year', '<=', $thresholdYear);
    }
}
