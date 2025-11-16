<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leader extends Model
{
    protected $table = 'leaders';
    protected $primaryKey = 'nid';
    protected $fillable = ['nid', 'position_id', 'user_id'];
    public $incrementing = false;
    public $timestamps = true;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id', 'position_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'leader_id', 'nid');
    }
}
