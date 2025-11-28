<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\VerifyEmailUnjani;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable 
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
     public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailUnjani);
    }

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
        ];
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function adminSubmissions()
    {
        return $this->hasMany(Submission::class, 'admin_id');
    }

    // Tambahkan relasi leader
    public function leader()
    {
        return $this->hasOne(Leader::class);
    }

    public function getIsProfileCompleteAttribute(): bool
    {
        $this->loadMissing(['student', 'leader']);

        if ($this->hasRole('admin')) {
            return true;
        }

    
        if ($this->hasRole('mahasiswa')) {
            return (bool) $this->student;
        }
        if ($this->hasRole('pimpinan')) {
            return (bool) $this->leader;
        }

        // Default untuk role lain (jika ada)
        return true;
    }
}
