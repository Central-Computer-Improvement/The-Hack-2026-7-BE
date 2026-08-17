<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
se Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    protected $fillable = [
        'skill_id',
        'title',
        'provider',
        'description',
        'link',
    ];

    public function skill(): BelongsTo
    {
        return $this->belongsTo(skill::class);
    }
}
