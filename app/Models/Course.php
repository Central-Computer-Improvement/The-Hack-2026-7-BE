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
        'category',
        'format',
        'duration',
        'rating',
        'thumbnail_url',
        'video_url',
        'provider',
        'location',
        'description',
        'link',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
    ];

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}
