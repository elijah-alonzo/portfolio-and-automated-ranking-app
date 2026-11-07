<?php

namespace App\Features\Evaluations\Models;

use App\Features\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationPeerEvaluator extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'evaluatee_user_id',
        'evaluator_user_id',
        'assigned_by_user_id',
        'assignment_notes',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    /** @return BelongsTo<Evaluation, $this> */
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    /** @return BelongsTo<User, $this> */
    public function evaluateeUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluatee_user_id');
    }

    /** @return BelongsTo<User, $this> */
    public function evaluatorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_user_id');
    }

    /** @return BelongsTo<User, $this> */
    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    /**
     * Check if a user can evaluate another user as peer
     * Returns true if the evaluator is assigned to evaluate the evaluatee
     */
    public static function canEvaluateAsPeer(int $evaluationId, int $evaluatorUserId, int $evaluateeUserId): bool
    {
        // Users cannot evaluate themselves
        if ($evaluatorUserId === $evaluateeUserId) {
            return false;
        }

        // Check if this peer assignment exists
        return static::where('evaluation_id', $evaluationId)
            ->where('evaluator_user_id', $evaluatorUserId)
            ->where('evaluatee_user_id', $evaluateeUserId)
            ->exists();
    }
}
