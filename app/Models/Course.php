<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill_id',
        'title',
        'provider',
        'location',
        'description',
        'link',
    ];

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}