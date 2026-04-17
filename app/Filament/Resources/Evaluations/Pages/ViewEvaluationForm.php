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

    protected string $view = 'EvaluationForm.AdviserEvaluation';

    public ?Evaluation $evaluation = null;
    public ?User $evaluatee = null;
    public string $evaluationType = '';
    public ?EvaluationForm $existingForm = null;
    public bool $isLocked = true;
    public array $data = [];
    public array $questions = [];
    public ?int $lengthOfServiceYears = null;
    public ?int $lengthOfServiceScore = null;
    public ?string $lengthOfServiceAwardType = null;

    public function mount(Evaluation $evaluation, User $user, string $type): void
    {
        $this->evaluation = $evaluation;
        $this->evaluatee = $user;
        $this->evaluationType = $type;

        $this->evaluation->loadMissing('council.awardType');

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

        if (auth()->user()->role !== 'admin') {
            abort(403, 'Admin access required');
        }

        $this->questions = EvaluationForm::getQuestionsForEvaluator($this->evaluationType);

        $serviceData = EvaluationForm::getLengthOfServiceData($this->evaluation, $this->evaluatee->id);
        $this->lengthOfServiceYears = $serviceData['years'];
        $this->lengthOfServiceScore = $serviceData['score'];
        $this->lengthOfServiceAwardType = $serviceData['award_type'];

        $this->loadExistingEvaluation();

        if ($this->existingForm) {
            $this->data = $this->existingForm->answers ?? [];
        }

        if (
            $this->evaluationType === 'adviser'
            && isset($this->questions[EvaluationForm::LENGTH_OF_SERVICE_KEY])
        ) {
            $this->data[EvaluationForm::LENGTH_OF_SERVICE_KEY] = $this->lengthOfServiceScore ?? 0;
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
                ->label('Back to Students')
                ->url(EvaluationResource::getUrl('view', ['record' => $this->evaluation])),
            Action::make('download_csv')
                ->label('Download CSV')
                ->color('info')
                ->url(fn () => route('evaluation.export', [
                    'evaluation' => $this->evaluation->id,
                    'user' => $this->evaluatee->id,
                    'type' => $this->evaluationType,
                    'format' => 'csv',
                ])),
            Action::make('download_pdf')
                ->label('Download PDF')
                ->color('gray')
                ->url(fn () => route('evaluation.export', [
                    'evaluation' => $this->evaluation->id,
                    'user' => $this->evaluatee->id,
                    'type' => $this->evaluationType,
                    'format' => 'pdf',
                ])),
        ];
    }
}