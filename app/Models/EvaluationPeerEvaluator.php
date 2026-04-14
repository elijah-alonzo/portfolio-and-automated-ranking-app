<?php

namespace App\Models;

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

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function evaluateeUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluatee_user_id');
    }

    public function evaluatorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_user_id');
    }

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

    /**
     * Get the students that a specific peer evaluator is assigned to evaluate
     */
    public static function getAssignedEvaluatees(int $evaluationId, int $evaluatorUserId): array
    {
        return static::where('evaluation_id', $evaluationId)
            ->where('evaluator_user_id', $evaluatorUserId)
            ->pluck('evaluatee_user_id')
            ->toArray();
    }

    /**
     * Get the peer evaluators assigned to a specific student
     */
    public static function getAssignedPeerEvaluators(int $evaluationId, int $evaluateeUserId): array
    {
        return static::where('evaluation_id', $evaluationId)
            ->where('evaluatee_user_id', $evaluateeUserId)
            ->pluck('evaluator_user_id')
            ->toArray();
    }
}
