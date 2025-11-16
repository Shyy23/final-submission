<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionMember extends Model
{
    protected $table = 'submission_members';
    protected $primaryKey = 'member_id';
    protected $fillable = [
        'submission_id',
        'student_nim',
        'is_representative',
    ];

    public $timestamps = true;

    public function student()
    {
        return $this->belongsTo(Student::class, 'nim', 'nim');
    }
}
