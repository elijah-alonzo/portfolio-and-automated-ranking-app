<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AwardType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the leadership award applications for this award type.
     */
    public function leadershipAwardApplications(): HasMany
    {
        return $this->hasMany(LeadershipAwardApplication::class);
    }

    /**
     * Get the councils for this award type.
     */
    public function councils(): HasMany
    {
        return $this->hasMany(Council::class);
    }
}