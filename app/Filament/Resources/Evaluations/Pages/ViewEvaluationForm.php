<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Models\Evaluation;
use App\Models\EvaluationForm;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ViewEvaluationForm extends Page
{
    protected static string $resource = EvaluationResource::class;
    protected static bool $shouldRegisterNavigation = false;
    
    protected string $view = 'EvaluationForm.AdviserEvaluation'; // Will be set dynamically in mount()

    public ?Evaluation $evaluation = null;
    public ?User $evaluatee = null;
    public string $evaluationType = '';
    public ?EvaluationForm $existingForm = null;
    public bool $isLocked = true; // Always locked for admin view
    public array $data = [];
    public array $questions = [];

    public function mount(Evaluation $evaluation, User $user, string $type): void
    {
        $this->evaluation = $evaluation;
        $this->evaluatee = $user;
        $this->evaluationType = $type;

        // Set the appropriate view based on evaluation type
        $this->view = match($this->evaluationType) {
            'self' => 'EvaluationForm.SelfEvaluation',
            'peer' => 'EvaluationForm.PeerEvaluation',
            'adviser' => 'EvaluationForm.AdviserEvaluation',
            default => 'EvaluationForm.AdviserEvaluation'
        };

        if (!$this->evaluation) {
            abort(404, 'Evaluation not found');
        }

        if (!$this->evaluatee) {
            abort(404, 'User not specified');
        }

        // Only allow admin access
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Admin access required');
        }

        // Load questions for this evaluator type
        $this->questions = EvaluationForm::getQuestionsForEvaluator($this->evaluationType);

        // Load existing evaluation
        $this->loadExistingEvaluation();

        // Pre-fill form data if exists
        if ($this->existingForm) {
            $this->data = $this->existingForm->answers ?? [];
        }
    }

    protected function loadExistingEvaluation(): void
    {
        $this->existingForm = EvaluationForm::where([
            'evaluation_id' => $this->evaluation->id,
            'user_id' => $this->evaluatee->id,
            'evaluator_type' => $this->evaluationType,
        ])->first();
    }

    public function getTitle(): string|Htmlable
    {
        $evaluationTypeLabel = match ($this->evaluationType) {
            'adviser' => 'Adviser',
            'peer' => 'Peer',
            'self' => 'Self',
            default => 'Unknown'
        };

        $targetName = $this->evaluationType === 'self' ? $this->evaluatee->name : $this->evaluatee->name;
        
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
                ->label('Back to Students')
                ->url(EvaluationResource::getUrl('view', ['record' => $this->evaluation]))
                ->color('gray'),
        ];
    }
}