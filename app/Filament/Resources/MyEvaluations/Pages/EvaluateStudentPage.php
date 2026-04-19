<?php

namespace App\Filament\Resources\MyEvaluations\Pages;

use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use App\Models\Evaluation;
use App\Models\EvaluationForm;
use App\Models\EvaluationPeerEvaluator;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Panel;
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

    public ?int $lengthOfServiceYears = null;

    public ?int $lengthOfServiceScore = null;

    public ?string $lengthOfServiceAwardType = null;

    public bool $isAssignedAdviser = false;

    public function mount(Evaluation $evaluation, User $user, string $type): void
    {
        $this->evaluation = $evaluation;
        $this->evaluatee = $user;
        $this->evaluationType = $type;
        $this->isAssignedAdviser = auth()->id() === $this->evaluation->council_adviser_id;

        $this->evaluation->loadMissing('council.awardType');

        if (! $this->evaluation) {
            abort(404, 'Evaluation not found');
        }

        if (! $this->evaluatee) {
            abort(404, 'User not specified');
        }

        $this->validatePermissions();

        $this->questions = EvaluationForm::getQuestionsForEvaluator($this->evaluationType);

        $serviceData = EvaluationForm::getLengthOfServiceData($this->evaluation, $this->evaluatee->id);
        $this->lengthOfServiceYears = $serviceData['years'];
        $this->lengthOfServiceScore = $serviceData['score'];
        $this->lengthOfServiceAwardType = $serviceData['award_type'];

        $this->loadExistingEvaluation();

        $this->isLocked = ($this->existingForm && $this->existingForm->status === 'submitted')
            || $this->evaluation->status === Evaluation::STATUS_COMPLETED
            || ($this->isAssignedAdviser && $this->evaluationType !== 'adviser')
            || (auth()->user()->role === 'admin' && ! $this->isAssignedAdviser);

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

    protected function validatePermissions(): void
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return;
        }

        if ($this->isAssignedAdviser) {
            return;
        }

        switch ($this->evaluationType) {
            case 'adviser':
                if ($this->evaluation->council_adviser_id !== $user->id) {
                    abort(403, 'You are not authorized to evaluate as an adviser.');
                }
                break;

            case 'peer':
                if (! EvaluationPeerEvaluator::canEvaluateAsPeer(
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

                if (! $isParticipating) {
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

        if (auth()->user()->role !== 'admin' && ! $this->isAssignedAdviser) {
            $query->where('evaluator_id', auth()->id());
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
        $actions = [
            Action::make('back')
                ->label('Back to Evaluation Details')
                ->url(MyEvaluationResource::getUrl('view', ['record' => $this->evaluation]))
                ->color('primary'),
        ];

        if ($this->isAssignedAdviser) {
            $actions[] = Action::make('download_csv')
                ->label('Download CSV')
                ->color('info')
                ->url(fn () => route('evaluation.export', [
                    'evaluation' => $this->evaluation->id,
                    'user' => $this->evaluatee->id,
                    'type' => $this->evaluationType,
                    'format' => 'csv',
                ]));

            $actions[] = Action::make('download_pdf')
                ->label('Download PDF')
                ->color('gray')
                ->url(fn () => route('evaluation.export', [
                    'evaluation' => $this->evaluation->id,
                    'user' => $this->evaluatee->id,
                    'type' => $this->evaluationType,
                    'format' => 'pdf',
                ]));
        }

        return $actions;
    }

    public static function getRouteName(?Panel $panel = null): string
    {
        return 'evaluate-student';
    }
}
