<?php

namespace App\Features\Evaluations\Models;

use App\Features\Councils\Models\Council;
use App\Features\Evaluations\Enums\RankTier;
use App\Features\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationRank extends Model
{
    use HasFactory;

    protected $table = 'evaluation_ranks';

    protected $fillable = [
        'evaluation_id',
        'user_id',
        'council_id',
        'final_score',
        'rank',
        'status',
    ];

    protected $casts = [
        'final_score' => 'decimal:3',
        'rank' => RankTier::class,
    ];

    /** @return BelongsTo<Evaluation, $this> */
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<Council, $this> */
    public function council(): BelongsTo
    {
        return $this->belongsTo(Council::class, 'council_id');
    }

    public function getRankDisplayAttribute(): string
    {
        return $this->rank?->label() ?? 'Pending';
    }
}
