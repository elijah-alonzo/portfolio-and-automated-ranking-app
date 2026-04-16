<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'branch',
        'hierarchy',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function councilAssignments(): HasMany
    {
        return $this->hasMany(CouncilPosition::class);
    }

    public function councils(): BelongsToMany
    {
        return $this->belongsToMany(Council::class, 'council_positions')
            ->withPivot(['max_slots', 'is_active'])
            ->withTimestamps();
    }
}
