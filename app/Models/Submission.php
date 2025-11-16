<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Submission extends Model
{
    protected $table = 'submissions';
    protected $primaryKey = 'submission_id';
    protected $fillable = ['representative_nim', 'admin_id', 'leader_id', 'company_name', 'address_company', 'note', 'status', 'file_submission', 'qr_url', 'feedback'];
    public $timestamps = true;

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
}
