<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Submission extends Model
{
    protected $table = 'submissions';
    protected $primaryKey = 'submission_id';
    protected $appends = ['end_date'];
    protected $fillable = ['representative_nim', 'admin_id', 'leader_id', 'company_name', 'address_company', 'note', 'status', 'document_path','sent_to_leader', 'qr_url', 'feedback', 'start_date', 'duration_days'];
    public $timestamps = true;

    protected $casts = [
        'start_date' => 'date',
        'sent_to_leader' => 'boolean',
    ];
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($submission) {
            // Hapus semua anggota submission
            $submission->members()->delete();
        });
    }

    public function representative()
    {
        return $this->belongsTo(Student::class, 'representative_nim', 'nim');
    }

    public function members()
    {
        return $this->hasMany(SubmissionMember::class, 'submission_id');
    }

    public function memberStudents()
    {
        return $this->belongsToMany(
            Student::class,
            'submission_members',
            'submission_id',
            'student_nim'
        )->withPivot('is_representative');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function leader()
    {
        return $this->belongsTo(Leader::class, 'leader_id', 'nid');
    }


    public function getEndDateAttribute()
    {
        if (!$this->start_date || !$this->duration_days) {
            return null;
        }

        return Carbon::parse($this->start_date)
                    ->addDays($this->duration_days)
                    ->format('Y-m-d');
    }

// Untuk keperluan query (scope)
    public function scopeActive($query)
    {
        return $query->where('status', 'verified')
                     ->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }

}
