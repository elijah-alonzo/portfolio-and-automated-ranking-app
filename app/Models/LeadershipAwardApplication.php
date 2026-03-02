<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadershipAwardApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'award_type_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the user that owns this application.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the award type for this application.
     */
    public function awardType(): BelongsTo
    {
        return $this->belongsTo(AwardType::class);
    }
}