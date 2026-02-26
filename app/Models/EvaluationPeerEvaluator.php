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
     * Get the student that a specific peer evaluator is assigned to evaluate
     * Returns the evaluatee user ID or null if not assigned
     * NOTE: Each peer evaluator can only evaluate ONE student per evaluation
     */
    public static function getAssignedEvaluatee(int $evaluationId, int $evaluatorUserId): ?int
    {
        return static::where('evaluation_id', $evaluationId)
            ->where('evaluator_user_id', $evaluatorUserId)
            ->value('evaluatee_user_id');
    }

    /**
     * Get the peer evaluator assigned to a specific student
     * Returns the evaluator user ID or null if not assigned
     * NOTE: Each student can only have ONE peer evaluator per evaluation
     */
    public static function getAssignedPeerEvaluator(int $evaluationId, int $evaluateeUserId): ?int
    {
        return static::where('evaluation_id', $evaluationId)
            ->where('evaluatee_user_id', $evaluateeUserId)
            ->value('evaluator_user_id');
    }

    /**
     * Check if a student already has a peer evaluator assigned
     */
    public static function hasAssignedPeerEvaluator(int $evaluationId, int $evaluateeUserId): bool
    {
        return static::where('evaluation_id', $evaluationId)
            ->where('evaluatee_user_id', $evaluateeUserId)
            ->exists();
    }
}
