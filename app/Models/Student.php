<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';
    protected $primaryKey = 'nim';
    protected $fillable = ['nim', 'study_id', 'user_id'];
    public $incrementing = false;
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function representativeSubmissions()
    {
        return $this->hasMany(Submission::class, 'representative_nim', 'nim');
    }

    public function studyProgram()
    {
        return $this->belongsTo(StudyProgram::class, 'study_id', 'study_id');
    }

    public function submissions()
    {
        return $this->belongsToMany(
            Submission::class,
            'submission_members',
            'student_nim',
            'submission_id'
        )->orWhere('representative_nim', $this->nim);
    }
    public function allSubmissions()
    {
        return $this->hasManyThrough(
            Submission::class,
            SubmissionMember::class,
            'student_nim',
            'submission_id',
            'nim',
            'submission_id'
        )->orWhere('representative_nim', $this->nim);
    }
}
