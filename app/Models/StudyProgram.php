<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyProgram extends Model
{
    protected $table = 'study_programs';
    protected $primaryKey = 'study_id';
    protected $fillable = ['study_name'];
    public $timestamps = true;
}
