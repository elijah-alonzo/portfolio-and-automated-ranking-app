<?php

namespace App\Features\Councils\Models;

use App\Features\Positions\Models\Position;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouncilPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'council_id',
        'position_id',
        'max_slots',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<Council, $this> */
    public function council(): BelongsTo
    {
        return $this->belongsTo(Council::class);
    }

    /** @return BelongsTo<Position, $this> */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
