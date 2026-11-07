<?php

namespace App\Features\Councils\Models;

use App\Features\Awards\Models\AwardType;
use App\Features\Departments\Models\Department;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Positions\Models\Position;
use App\Features\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
     *
     * @return HasManyThrough<User, Evaluation, $this>
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
     *
     * @return HasMany<Evaluation, $this>
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    /** @return BelongsToMany<Position, $this> */
    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'council_positions')
            ->withPivot(['max_slots', 'is_active'])
            ->withTimestamps();
    }

    /** @return BelongsToMany<Department, $this> */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'council_department')
            ->withTimestamps();
    }

    /**
     * Get the award type for this council.
     *
     * @return BelongsTo<AwardType, $this>
     */
    public function awardType(): BelongsTo
    {
        return $this->belongsTo(AwardType::class);
    }
}
