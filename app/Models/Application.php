<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Application extends Model
{
    protected $fillable = [
        'user_id',
        'job_posting_id',
        'message',
        'status',
        'applied_at',
    ];

    protected $casts = [
        'applied_at' => 'title'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }
}
