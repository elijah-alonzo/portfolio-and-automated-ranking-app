<?php

namespace App\Filament\Resources\MyEvaluations\RelationManagers;

use App\Models\CouncilPosition;
use App\Models\EvaluationForm;
use App\Models\EvaluationPeerEvaluator;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'users';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $title = 'Students';

    public function table(Table $table): Table
    {
        return $table
            ->columns($this->getTableColumns())
            ->headerActions($this->getHeaderActions())
            ->actions($this->getTableActions())
            ->filters([])
            ->bulkActions([])
            ->striped();
    }

    protected function getTableColumns(): array
    {
        return [
            ColumnGroup::make('Student', [
                ImageColumn::make('pfp')
                    ->label('Profile')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF'),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('pivot.position')
                    ->label('Position')
                    ->placeholder('No position assigned'),
            ]),
            ColumnGroup::make('Evaluation Status', [
                IconColumn::make('self_status')
                    ->label('Self')
                    ->state(fn () => true)
                    ->icon(fn ($record) => $this->getEvaluationStatus($record->id, 'self') === 'submitted' ? 'heroicon-o-check-circle' : 'heroicon-o-clock')
                    ->color(fn ($record) => $this->getEvaluationStatus($record->id, 'self') === 'submitted' ? 'success' : 'warning'),
                IconColumn::make('peer_status')
                    ->label('Peer')
                    ->state(fn () => true)
                    ->icon(fn ($record) => $this->getEvaluationStatus($record->id, 'peer') === 'submitted' ? 'heroicon-o-check-circle' : 'heroicon-o-clock')
                    ->color(fn ($record) => $this->getEvaluationStatus($record->id, 'peer') === 'submitted' ? 'success' : 'warning'),
                IconColumn::make('adviser_status')
                    ->label('Adviser')
                    ->state(fn () => true)
                    ->icon(fn ($record) => $this->getEvaluationStatus($record->id, 'adviser') === 'submitted' ? 'heroicon-o-check-circle' : 'heroicon-o-clock')
                    ->color(fn ($record) => $this->getEvaluationStatus($record->id, 'adviser') === 'submitted' ? 'success' : 'warning'),
            ]),
        ];
    }


    protected function getEvaluationStatus(int $userId, string $evaluatorType): string
    {
        $status = \App\Models\EvaluationForm::where('evaluation_id', $this->ownerRecord->id)
            ->where('user_id', $userId)
            ->where('evaluator_type', $evaluatorType)
            ->value('status');
        return $status ?? 'pending';
    }

    protected function getHeaderActions(): array
    {
        if (!$this->isCouncilAdviser()) {
            return [];
        }

        $actions = [];

        if (!$this->ownerRecord->is_open) {
            $actions[] = AttachAction::make()
                ->label('Add Officer')
                ->color('success')
                ->form($this->getAttachForm())
                ->preloadRecordSelect()
                ->modalHeading('Add Student to Evaluation')
                ->modalDescription('Add a new student and optionally assign peer evaluatees')
                ->modalWidth('lg')
                ->before(function (AttachAction $action, array $data) {
                    $existingUser = $this->ownerRecord->users()
                        ->where('user_id', $data['recordId'])
                        ->exists();

                    if ($existingUser) {
                        Notification::make()
                            ->title('User Already Added')
                            ->body('This user is already assigned to this evaluation.')
                            ->warning()
                            ->send();

                        $action->halt();
                    }

                    if (!$this->canAssignPosition($data['position'] ?? null)) {
                        $action->halt();
                    }
                })
                ->after(function (AttachAction $action, array $data, $record) {
                    if (isset($data['peer_evaluatee']) && !empty($data['peer_evaluatee'])) {
                        $this->assignPeerEvaluatee($data['recordId'], $data['peer_evaluatee']);
                    }
                });
        }

        $actions[] = Action::make('toggle_evaluation')
            ->label(fn () => $this->ownerRecord->is_open ? 'Close Evaluation' : 'Open Evaluation')
            ->color(fn () => $this->ownerRecord->is_open ? 'danger' : 'warning')
            ->requiresConfirmation()
            ->action(function () {
                $this->ownerRecord->update([
                    'is_open' => !$this->ownerRecord->is_open,
                ]);

                $this->ownerRecord->refresh();

                $this->resetTable();

                Notification::make()
                    ->title($this->ownerRecord->is_open ? 'Evaluation Opened' : 'Evaluation Closed')
                    ->body($this->ownerRecord->is_open
                        ? 'Students can now start their evaluations.'
                        : 'Evaluations are currently closed.'
                    )
                    ->success()
                    ->send();
            });

        return $actions;
    }

    protected function getTableActions(): array
    {
        $user = auth()->user();
        $isAdviser = $this->isCouncilAdviser();
        $isStudent = $user && $user->role === 'student';

        $actions = [];

        $actions[] = Action::make('evaluate')
            ->label('Evaluate')
            ->icon('heroicon-o-clipboard-document-check')
            ->color('success')
            ->size('sm')
            ->url(function ($record) use ($user, $isAdviser, $isStudent) {
                if ($isAdviser) {
                    return $this->ownerRecord->getEvaluationUrl($record->id, 'adviser');
                }
                if ($isStudent) {
                    if ($user->id === $record->id) {
                        return $this->ownerRecord->getEvaluationUrl($record->id, 'self');
                    }
                    $isPeerEvaluator = \App\Models\EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->where('evaluator_user_id', $user->id)
                        ->where('evaluatee_user_id', $record->id)
                        ->exists();
                    if ($isPeerEvaluator) {
                        return $this->ownerRecord->getEvaluationUrl($record->id, 'peer');
                    }
                }
                return null;
            })
            ->tooltip(function ($record) use ($user, $isAdviser, $isStudent) {
                if (!$this->ownerRecord->is_open) {
                    return 'Evaluation is not open yet';
                }
                if ($isAdviser) {
                    return 'Complete adviser evaluation for this student';
                }
                if ($isStudent) {
                    if ($user->id === $record->id) {
                        return 'Complete your self evaluation';
                    }
                    $isPeerEvaluator = \App\Models\EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->where('evaluator_user_id', $user->id)
                        ->where('evaluatee_user_id', $record->id)
                        ->exists();
                    if ($isPeerEvaluator) {
                        return 'Complete peer evaluation for this student';
                    }
                    return 'You are not assigned to evaluate this student';
                }
                return '';
            })
            ->disabled(function ($record) use ($user, $isAdviser, $isStudent) {
                if (!$this->ownerRecord->is_open) {
                    return true;
                }
                if ($isAdviser) {
                    return false;
                }
                if ($isStudent) {
                    if ($user->id === $record->id) {
                        return false;
                    }
                    $isPeerEvaluator = \App\Models\EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->where('evaluator_user_id', $user->id)
                        ->where('evaluatee_user_id', $record->id)
                        ->exists();
                    return !$isPeerEvaluator;
                }
                return true;
            });

        if ($isAdviser && !$this->ownerRecord->is_open) {
            $actions[] = EditAction::make()
                ->color('info')
                ->form($this->getEditForm())
                ->action(function ($record, $data) {
                    if (!$this->canAssignPosition($data['position'] ?? null, $record->id)) {
                        return;
                    }

                    $record->pivot->update(['position' => $data['position']]);

                    if (isset($data['peer_evaluatee'])) {
                        $this->assignPeerEvaluatee($record->id, $data['peer_evaluatee']);
                    }
                })
                ->modalHeading(fn ($record) => 'Edit ' . $record->name)
                ->modalDescription('Update student details and peer evaluation assignments')
                ->modalWidth('lg');

            $actions[] = DetachAction::make()
                ->label('Remove')
                ->color('danger')
                ->after(function ($record) {
                    EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->where(function ($query) use ($record) {
                            $query->where('evaluatee_user_id', $record->id)
                                  ->orWhere('evaluator_user_id', $record->id);
                        })
                        ->delete();
                });
        }

        return [
            ActionGroup::make($actions)
                ->label('')
                ->icon('heroicon-m-ellipsis-vertical')
                ->iconButton(),
        ];
    }

    protected function canAssignPosition(?string $positionTitle, ?int $userId = null): bool
    {
        if (!$positionTitle) {
            return false;
        }

        $position = CouncilPosition::query()
            ->where('council_id', $this->ownerRecord->council_id)
            ->where('title', $positionTitle)
            ->where('is_active', true)
            ->first();

        if (!$position) {
            Notification::make()
                ->title('Position Not Available')
                ->body('Selected position is not available for this council.')
                ->warning()
                ->send();
            return false;
        }

        $assignedCount = $this->ownerRecord->users()
            ->wherePivot('position', $positionTitle)
            ->when($userId, fn ($query) => $query->where('users.id', '!=', $userId))
            ->count();

        if ($assignedCount >= $position->max_slots) {
            Notification::make()
                ->title('Position Full')
                ->body("{$positionTitle} already has the maximum of {$position->max_slots} slot(s) in this evaluation.")
                ->warning()
                ->send();
            return false;
        }

        return true;
    }

    protected function getAttachForm(): array
    {
        return [
            Select::make('recordId')
                ->label('Student')
                ->options(function () {
                    return User::where('role', 'student')
                        ->whereNotIn('id', $this->ownerRecord->users->pluck('id'))
                        ->pluck('name', 'id');
                })
                ->searchable()
                ->required()
                ->placeholder('Select a student')
                ->prefixIcon('heroicon-m-user'),

            Select::make('position')
                ->label('Position')
                ->options(fn () => $this->getPositionOptions())
                ->required()
                ->searchable()
                ->placeholder('Select a position')
                ->prefixIcon('heroicon-m-identification'),

            Select::make('peer_evaluatee')
                ->label('Who will this student evaluate?')
                ->options(function () {
                    $allStudentIds = $this->ownerRecord->users()->pluck('users.id')->toArray();
                    $assignedEvaluateeIds = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->pluck('evaluatee_user_id')
                        ->toArray();
                    $availableIds = array_diff($allStudentIds, $assignedEvaluateeIds);
                    
                    return $this->ownerRecord->users()
                        ->whereIn('users.id', $availableIds)
                        ->pluck('name', 'users.id')
                        ->toArray();
                })
                ->searchable()
                ->placeholder('Select students that this user will evaluate .')
        ];
    }

    protected function getEditForm(): array
    {
        return [
            Select::make('position')
                ->label('Position')
                ->options(fn ($record) => $this->getPositionOptions($record?->id))
                ->required()
                ->searchable()
                ->placeholder('Select a position')
                ->prefixIcon('heroicon-m-identification'),

            Select::make('peer_evaluatee')
                ->label('Assign Student to Evaluate (Peer Evaluatee)')
                ->options(function ($record) {
                    $allUserIds = $this->ownerRecord->users()
                        ->where('users.id', '!=', $record->id)
                        ->pluck('users.id')
                        ->toArray();

                    $alreadyAssignedIds = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->whereIn('evaluatee_user_id', $allUserIds)
                        ->where('evaluator_user_id', '!=', $record->id)
                        ->pluck('evaluatee_user_id')
                        ->toArray();

                    $eligibleIds = array_diff($allUserIds, $alreadyAssignedIds);

                    $currentAssignedId = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->where('evaluator_user_id', $record->id)
                        ->value('evaluatee_user_id');

                    if ($currentAssignedId) {
                        $eligibleIds[] = $currentAssignedId;
                    }

                    return $this->ownerRecord->users()
                        ->whereIn('users.id', $eligibleIds)
                        ->pluck('name', 'users.id')
                        ->toArray();
                })
                ->default(function ($record) {
                    return EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->where('evaluator_user_id', $record->id)
                        ->value('evaluatee_user_id');
                })
                ->searchable()
                ->placeholder('Select one student to evaluate')
                ->helperText('⚠️ One-to-one assignment: Each student can only have ONE peer evaluator. Only unassigned students are shown.')
        ];
    }

    protected function getPositionOptions(?int $userId = null): array
    {
        $positions = CouncilPosition::query()
            ->where('council_id', $this->ownerRecord->council_id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['title', 'max_slots']);

        $assignedCounts = $this->ownerRecord->users()
            ->when($userId, fn ($query) => $query->where('users.id', '!=', $userId))
            ->pluck('evaluation_user.position')
            ->filter()
            ->countBy();

        $currentPosition = null;
        if ($userId) {
            $currentPosition = $this->ownerRecord->users()
                ->where('users.id', $userId)
                ->value('evaluation_user.position');
        }

        return $positions
            ->filter(function ($position) use ($assignedCounts, $currentPosition) {
                $assigned = (int) ($assignedCounts[$position->title] ?? 0);
                if ($currentPosition && $position->title === $currentPosition) {
                    return true;
                }
                return $assigned < $position->max_slots;
            })
            ->pluck('title', 'title')
            ->toArray();
    }

    protected function getEvaluationScore(int $userId, string $evaluatorType): string
    {
        $score = EvaluationForm::where('evaluation_id', $this->ownerRecord->id)
            ->where('user_id', $userId)
            ->where('evaluator_type', $evaluatorType)
            ->first();

        if ($score && $score->evaluator_score !== null) {
            return number_format($score->evaluator_score, 2);
        }
        
        return '-';
    }

    protected function assignPeerEvaluatee(int $evaluatorUserId, ?int $evaluateeId): void
    {
        try {
            EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                ->where('evaluator_user_id', $evaluatorUserId)
                ->delete();

            if ($evaluateeId) {
                $existingEvaluator = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                    ->where('evaluatee_user_id', $evaluateeId)
                    ->first();

                if ($existingEvaluator) {
                    $currentEvaluatorName = User::find($existingEvaluator->evaluator_user_id)->name;
                    $evaluateeName = User::find($evaluateeId)->name;
                    
                    Notification::make()
                        ->title('Student Already Has a Peer Evaluator')
                        ->body("{$evaluateeName} is already being evaluated by {$currentEvaluatorName}. Each student can only have ONE peer evaluator.")
                        ->warning()
                        ->send();
                    return;
                }

                EvaluationPeerEvaluator::create([
                    'evaluation_id' => $this->ownerRecord->id,
                    'evaluatee_user_id' => $evaluateeId,
                    'evaluator_user_id' => $evaluatorUserId,
                    'assigned_by_user_id' => auth()->id(),
                    'assigned_at' => now(),
                ]);

                $evaluatorName = User::find($evaluatorUserId)->name;
                $evaluateeName = User::find($evaluateeId)->name;
                
                Notification::make()
                    ->title('Peer Evaluator Assigned Successfully')
                    ->body("{$evaluatorName} will evaluate {$evaluateeName} (one-to-one assignment)")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Peer Evaluation Assignment Removed')
                    ->body('The peer evaluator assignment has been removed.')
                    ->info()
                    ->send();
            }
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error Assigning Peer Evaluator')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function isCouncilAdviser(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        return $this->ownerRecord->council_adviser_id === $user->id;
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    protected function canAttach(): bool
    {
        return $this->isCouncilAdviser();
    }

    protected function canEdit($record): bool
    {
        return $this->isCouncilAdviser();
    }

    protected function canDetach($record): bool
    {
        return $this->isCouncilAdviser();
    }
}