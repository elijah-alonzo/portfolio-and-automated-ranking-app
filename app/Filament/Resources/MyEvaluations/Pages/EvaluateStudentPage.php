<?php

namespace App\Filament\Resources\MyEvaluations\Pages;

use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use App\Models\Evaluation;
use App\Models\EvaluationForm;
use App\Models\EvaluationPeerEvaluator;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class EvaluateStudentPage extends Page
{
    protected static string $resource = MyEvaluationResource::class;
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'EvaluationForm.AdviserEvaluation';

    public ?Evaluation $evaluation = null;
    public ?User $evaluatee = null;
    public string $evaluationType = '';
    public ?EvaluationForm $existingForm = null;
    public bool $isLocked = false;
    public array $data = [];
    public array $questions = [];

    public function mount(Evaluation $evaluation, User $user, string $type): void
    {
        $this->evaluation = $evaluation;
        $this->evaluatee = $user;
        $this->evaluationType = $type;

        if (!$this->evaluation) {
            abort(404, 'Evaluation not found');
        }

        if (!$this->evaluatee) {
            abort(404, 'User not specified');
        }

        $this->validatePermissions();

        $this->questions = EvaluationForm::getQuestionsForEvaluator($this->evaluationType);

        $this->loadExistingEvaluation();

        $this->isLocked = (bool) ($this->existingForm?->status === 'submitted');

        if ($this->existingForm) {
            $this->data = $this->existingForm->answers ?? [];
        }
    }

    protected function validatePermissions(): void
    {
        $user = auth()->user();
        
        switch ($this->evaluationType) {
            case 'adviser':
                if ($this->evaluation->council_adviser_id !== $user->id) {
                    abort(403, 'You are not authorized to evaluate as an adviser.');
                }
                break;
                
            case 'peer':
                if (!EvaluationPeerEvaluator::canEvaluateAsPeer(
                    $this->evaluation->id, 
                    $user->id, 
                    $this->evaluatee->id
                )) {
                    abort(403, 'You are not authorized to evaluate this user as a peer.');
                }
                break;
                
            case 'self':
                if ($this->evaluatee->id !== $user->id) {
                    abort(403, 'You can only perform self-evaluation on your own record.');
                }
                
                $isParticipating = $this->evaluation->users()
                    ->where('user_id', $user->id)
                    ->exists();
                    
                if (!$isParticipating) {
                    abort(403, 'You are not participating in this evaluation.');
                }
                break;
                
            default:
                abort(404, 'Invalid evaluation type');
        }
    }

    protected function loadExistingEvaluation(): void
    {
        $query = EvaluationForm::where([
            'evaluation_id' => $this->evaluation->id,
            'user_id' => $this->evaluatee->id,
            'evaluator_type' => $this->evaluationType,
        ]);

        if ($this->evaluationType === 'peer') {
            $peerAssignmentId = EvaluationPeerEvaluator::where('evaluation_id', $this->evaluation->id)
                ->where('evaluator_user_id', auth()->id())
                ->where('evaluatee_user_id', $this->evaluatee->id)
                ->value('id');

            $query->where('evaluation_peer_evaluator_id', $peerAssignmentId);
        }

        $this->existingForm = $query->first();
    }

    public function getTitle(): string|Htmlable
    {
        $evaluationTypeLabel = match ($this->evaluationType) {
            'adviser' => 'Adviser',
            'peer' => 'Peer',
            'self' => 'Self',
            default => 'Unknown'
        };

        $targetName = $this->evaluatee->name ?? 'Unknown';
        
        return "{$evaluationTypeLabel} Evaluation for {$targetName}";
    }

    public function getSubheading(): string|Htmlable|null
    {
        $councilName = $this->evaluation->council->name ?? 'Council';
        $academicYear = $this->evaluation->academic_year ?? 'Unknown Year';
        
        return "Council: {$councilName} | Academic Year: {$academicYear}";
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Evaluation Details')
                ->url(MyEvaluationResource::getUrl('view', ['record' => $this->evaluation]))
                ->color('gray'),
        ];
    }

    public static function getRouteName(?\Filament\Panel $panel = null): string
    {
        return 'evaluate-student';
    }
}