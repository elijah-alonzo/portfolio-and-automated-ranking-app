<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Council extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'is_active',
        'description',
        'award_type_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the users for the council.
     */
    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            Evaluation::class,
            'council_id',
            'id',
            'id',
            'council_adviser_id'
        )->distinct();
    }

    /**
     * Get the evaluations for the council.
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    /**
     * Get the predefined positions for this council.
     */
    public function positions(): HasMany
    {
        return $this->hasMany(CouncilPosition::class);
    }

    /**
     * Get the award type for this council.
     */
    public function awardType()
    {
        return $this->belongsTo(AwardType::class);
    }
}
