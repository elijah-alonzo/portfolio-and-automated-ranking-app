<?php

namespace App\Models;

use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'council_id',
        'council_adviser_id',
        'academic_year',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public const STATUS_CLOSED = 'closed';

    public const STATUS_ONGOING = 'ongoing';

    public const STATUS_COMPLETED = 'completed';

    public function council(): BelongsTo
    {
        return $this->belongsTo(Council::class);
    }

    public function adviser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'council_adviser_id');
    }

    /**
     * The users that belong to this evaluation with their positions
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'evaluation_user')
            ->withPivot('position')
            ->withTimestamps();
    }

    /**
     * All peer evaluator assignments for this evaluation
     */
    public function peerEvaluators(): HasMany
    {
        return $this->hasMany(EvaluationPeerEvaluator::class);
    }

    public function positionSlots(): HasMany
    {
        return $this->hasMany(EvaluationPositionSlot::class);
    }

    /**
     * Generate URL for evaluating a specific user with the unified evaluation page
     */
    public function getEvaluationUrl(int $userId, string $evaluatorType): string
    {
        return MyEvaluationResource::getUrl(
            'evaluate-student',
            [
                'evaluation' => $this->id,
                'user' => $userId,
                'type' => $evaluatorType,
            ]
        );
    }

    protected static function booted(): void
    {
        static::created(function (Evaluation $evaluation) {
            $councilPositions = CouncilPosition::query()
                ->where('council_id', $evaluation->council_id)
                ->where('is_active', true)
                ->get(['position_id', 'max_slots']);

            foreach ($councilPositions as $councilPosition) {
                for ($slot = 1; $slot <= $councilPosition->max_slots; $slot++) {
                    EvaluationPositionSlot::create([
                        'evaluation_id' => $evaluation->id,
                        'position_id' => $councilPosition->position_id,
                        'slot_number' => $slot,
                        'user_id' => null,
                    ]);
                }
            }
        });
    }

    public function areAllFormsSubmitted(): bool
    {
        $assignedUserIds = $this->positionSlots()
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique()
            ->toArray();

        if (empty($assignedUserIds)) {
            return false;
        }

        $submittedForms = EvaluationForm::query()
            ->where('evaluation_id', $this->id)
            ->where('status', 'submitted')
            ->whereIn('user_id', $assignedUserIds)
            ->get(['user_id', 'evaluator_type'])
            ->groupBy('user_id');

        foreach ($assignedUserIds as $userId) {
            $types = $submittedForms->get($userId)?->pluck('evaluator_type')->unique()->toArray() ?? [];
            if (! in_array('self', $types, true) || ! in_array('peer', $types, true) || ! in_array('adviser', $types, true)) {
                return false;
            }
        }

        return true;
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function isOngoing(): bool
    {
        return $this->status === self::STATUS_ONGOING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
