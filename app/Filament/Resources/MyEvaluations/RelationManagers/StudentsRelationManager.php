<?php

namespace App\Filament\Resources\MyEvaluations\RelationManagers;

use App\Models\Evaluation;
use App\Models\EvaluationForm;
use App\Models\EvaluationPeerEvaluator;
use App\Models\EvaluationPositionSlot;
use App\Models\Position;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'positionSlots';
    protected static ?string $recordTitleAttribute = 'id';
    protected static ?string $title = 'Students';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $this->applyStageFilters($query))
            ->columns($this->getTableColumns())
            ->headerActions($this->getHeaderActions())
            ->actions($this->isCompletedStage() ? [] : $this->getTableActions())
            ->filters([])
            ->bulkActions([])
            ->striped();
    }

    protected function getTableColumns(): array
    {
        return [
            ColumnGroup::make('Student Information', [
                ImageColumn::make('user.pfp')
                    ->label('Picture')
                    ->circular()
                    ->size(40)
                    ->getStateUsing(function (EvaluationPositionSlot $record) {
                        $name = $record->user?->name ?? 'Unassigned';

                        return $record->user?->pfp
                            ? (str_starts_with($record->user->pfp, 'http')
                                ? $record->user->pfp
                                : asset('storage/' . $record->user->pfp))
                            : 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=EBF4FF';
                    }),
                TextColumn::make('user.name')
                    ->label('Student')
                    ->weight('medium')
                    ->placeholder('Unassigned')
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
                    })
                    ->description(fn (EvaluationPositionSlot $record) => $record->user?->department?->name ?? 'No department'),
            ]),
            TextColumn::make('position_slot')
                ->label('Position')
                ->getStateUsing(fn (EvaluationPositionSlot $record) => $this->getPositionSlotLabel($record))
                ->description(fn (EvaluationPositionSlot $record) => $this->getRecommendationForPosition($record->position?->title))
                ->wrap(),
            TextColumn::make('peer_evaluator')
                ->label('Peer Evaluator')
                ->getStateUsing(fn (EvaluationPositionSlot $record) => $this->getPeerEvaluatorName($record))
                ->badge(),
            ColumnGroup::make('Evaluation Status', [
                IconColumn::make('self_status')
                    ->label('Self')
                    ->state(fn () => true)
                    ->icon(fn (EvaluationPositionSlot $record) => $this->getStatusIcon($record, 'self'))
                    ->color(fn (EvaluationPositionSlot $record) => $this->getStatusColor($record, 'self')),
                IconColumn::make('peer_status')
                    ->label('Peer')
                    ->state(fn () => true)
                    ->icon(fn (EvaluationPositionSlot $record) => $this->getStatusIcon($record, 'peer'))
                    ->color(fn (EvaluationPositionSlot $record) => $this->getStatusColor($record, 'peer')),
                IconColumn::make('adviser_status')
                    ->label('Adviser')
                    ->state(fn () => true)
                    ->icon(fn (EvaluationPositionSlot $record) => $this->getStatusIcon($record, 'adviser'))
                    ->color(fn (EvaluationPositionSlot $record) => $this->getStatusColor($record, 'adviser')),
            ]),
        ];
    }

    protected function applyStageFilters(Builder $query): Builder
    {
        $query->with(['position', 'user']);

        if ($this->isClosedStage()) {
            return $query;
        }

        return $query->whereNotNull('user_id');
    }

    protected function getHeaderActions(): array
    {
        if (!$this->isCouncilAdviser()) {
            return [];
        }

        return [
            Action::make('toggle_evaluation')
                ->label(function () {
                    if ($this->isClosedStage()) {
                        return 'Open Evaluation';
                    }

                    if ($this->isOngoingStage()) {
                        return $this->ownerRecord->areAllFormsSubmitted()
                            ? 'Complete Evaluation'
                            : 'Close Evaluation';
                    }

                    return 'Evaluation Completed';
                })
                ->color(function () {
                    if ($this->isClosedStage()) {
                        return 'info';
                    }

                    if ($this->isOngoingStage()) {
                        return $this->ownerRecord->areAllFormsSubmitted()
                            ? 'success'
                            : 'danger';
                    }

                    return 'gray';
                })
                ->after(function (AttachAction $action, array $data, $record) {
                    Notification::make()
                        ->title('Added to Evaluation')
                        ->body("You were added to the {$this->ownerRecord->council->name} evaluation ({$this->ownerRecord->academic_year}).")
                        ->info()
                        ->sendToDatabase($record);

                    if (isset($data['peer_evaluatee']) && !empty($data['peer_evaluatee'])) {
                        $this->assignPeerEvaluatee($data['recordId'], $data['peer_evaluatee']);
                    }

                    if ($this->isOngoingStage() && $this->ownerRecord->areAllFormsSubmitted()) {
                        $this->ownerRecord->update([
                            'status' => Evaluation::STATUS_COMPLETED,
                        ]);
                    } elseif ($this->isClosedStage()) {
                        $this->ownerRecord->update([
                            'status' => Evaluation::STATUS_ONGOING,
                        ]);
                    } else {
                        $this->ownerRecord->update([
                            'status' => Evaluation::STATUS_CLOSED,
                        ]);
                    }

                    $this->ownerRecord->refresh();

                    $this->resetTable();

                    $this->notifyEvaluationStatusChange($previousStatus, $this->ownerRecord->status);

                    Notification::make()
                        ->title($this->getStatusNotificationTitle())
                        ->body($this->getStatusNotificationBody())
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getTableActions(): array
    {
        $actions = [];

        if ($this->isClosedStage()) {
            $actions[] = Action::make('assign_student')
                ->label(fn (EvaluationPositionSlot $record) => $record->user_id ? 'Change Student' : 'Assign Student')
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->modalIcon('heroicon-o-user-plus')
                ->modalIconColor('success')
                ->modalHeading('Assign Student')
                ->modalDescription('Select a student to fill this position slot.')
                ->modalWidth('md')
                ->form(fn (EvaluationPositionSlot $record) => [
                    \Filament\Forms\Components\Select::make('student_id')
                        ->label('Student')
                        ->options($this->getEligibleStudentOptions($record))
                        ->searchable()
                        ->required()
                        ->placeholder('Select a student'),
                ])
                ->action(function (EvaluationPositionSlot $record, array $data) {
                    $this->assignStudentToSlot($record, (int) $data['student_id']);
                });

            $actions[] = Action::make('assign_peer_evaluator')
                ->label('Assign Peer Evaluator')
                ->icon('heroicon-o-user-group')
                ->color('warning')
                ->modalIcon('heroicon-o-user-group')
                ->modalIconColor('warning')
                ->modalHeading('Assign Peer Evaluator')
                ->modalDescription('Select a peer evaluator for this student.')
                ->modalWidth('md')
                ->visible(fn (EvaluationPositionSlot $record) => (bool) $record->user_id)
                ->form(fn (EvaluationPositionSlot $record) => [
                    \Filament\Forms\Components\Select::make('evaluator_id')
                        ->label('Peer Evaluator')
                        ->options($this->getEligiblePeerEvaluatorOptions($record->user_id))
                        ->searchable()
                        ->required()
                        ->placeholder('Select a peer evaluator'),
                ])
                ->action(function (EvaluationPositionSlot $record, array $data) {
                    $this->assignPeerEvaluator($record->user_id, (int) $data['evaluator_id']);
                });

            $actions[] = Action::make('remove_student')
                ->label('Remove Student')
                ->icon('heroicon-o-user-minus')
                ->color('danger')
                ->visible(fn (EvaluationPositionSlot $record) => (bool) $record->user_id)
                ->requiresConfirmation()
                ->action(function (EvaluationPositionSlot $record) {
                    $this->removeStudentFromSlot($record);
                });
        }

        $actions[] = Action::make('evaluate')
            ->label(function (EvaluationPositionSlot $record) {
                $user = auth()->user();
                if (!$user || !$record->user_id) {
                    return 'Evaluate';
                }

                $type = $this->resolveEvaluationTypeForUser($record, $user);
                if (!$type) {
                    return 'Evaluate';
                }

                return $this->getEvaluationStatus($record->user_id, $type) === 'submitted'
                    ? 'View'
                    : 'Evaluate';
            })
            ->icon('heroicon-o-clipboard-document-check')
            ->color('success')
            ->size('sm')
            ->visible(fn (EvaluationPositionSlot $record) => $this->isOngoingStage() && (bool) $record->user_id)
            ->url(function (EvaluationPositionSlot $record) {
                $user = auth()->user();
                if (!$user || !$record->user_id) {
                    return null;
                }

                $isAdviser = $this->isCouncilAdviser();
                $isStudent = $user->role === 'student';

                if ($isAdviser) {
                    return $this->ownerRecord->getEvaluationUrl($record->user_id, 'adviser');
                }

                if ($isStudent) {
                    if ($user->id === $record->user_id) {
                        return $this->ownerRecord->getEvaluationUrl($record->user_id, 'self');
                    }

                    $isPeerEvaluator = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->where('evaluator_user_id', $user->id)
                        ->where('evaluatee_user_id', $record->user_id)
                        ->exists();

                    if ($isPeerEvaluator) {
                        return $this->ownerRecord->getEvaluationUrl($record->user_id, 'peer');
                    }
                }

                return null;
            })
            ->disabled(function (EvaluationPositionSlot $record) {
                $user = auth()->user();
                if (!$user || !$record->user_id) {
                    return true;
                }

                if (!$this->isOngoingStage()) {
                    return true;
                }

                if ($this->isCouncilAdviser()) {
                    return false;
                }

                if ($user->role === 'student') {
                    if ($user->id === $record->user_id) {
                        return false;
                    }

                    $isPeerEvaluator = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->where('evaluator_user_id', $user->id)
                        ->where('evaluatee_user_id', $record->user_id)
                        ->exists();

                    return !$isPeerEvaluator;
                }

                return true;
            });

        return [
            ActionGroup::make($actions)
                ->label('')
                ->icon('heroicon-m-ellipsis-vertical')
                ->iconButton(),
        ];
    }

    protected function assignStudentToSlot(EvaluationPositionSlot $slot, int $studentId): void
    {
        if ($this->isCompletedStage()) {
            Notification::make()
                ->title('Evaluation Completed')
                ->body('You cannot update assignments after completion.')
                ->warning()
                ->send();
            return;
        }

        if ($this->isStudentAlreadyAssigned($studentId, $slot->id)) {
            Notification::make()
                ->title('Student Already Assigned')
                ->body('This student is already assigned to another position slot.')
                ->warning()
                ->send();
            return;
        }

        if ($slot->user_id && $slot->user_id !== $studentId) {
            $this->removeStudentFromSlot($slot, silent: true);
        }

        $slot->update(['user_id' => $studentId]);

        $positionTitle = $slot->position?->title;
        if ($positionTitle) {
            $this->ownerRecord->users()->syncWithoutDetaching([
                $studentId => ['position' => $positionTitle],
            ]);
        }

        $student = User::find($studentId);
        if ($student) {
            Notification::make()
                ->title('Evaluation Assignment')
                ->body('You have been added to an evaluation.')
                ->success()
                ->sendToDatabase($student);
        }

        $adviser = $this->ownerRecord->adviser;
        if ($adviser) {
            Notification::make()
                ->title('Student Assigned')
                ->body(($student?->name ?? 'A student') . ' was added to your evaluation.')
                ->success()
                ->sendToDatabase($adviser);
        }

        Notification::make()
            ->title('Student Assigned')
            ->body('Student assigned to the selected slot.')
            ->success()
            ->send();
    }

    protected function removeStudentFromSlot(EvaluationPositionSlot $slot, bool $silent = false): void
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
                
            TextInput::make('position')
                ->label('Position')
                ->required()
                ->maxLength(255)
                ->placeholder('e.g., President, Secretary, Member')
                ->prefixIcon('heroicon-m-identification'),

            Select::make('peer_evaluatee')
                ->label('Assign Students to Evaluate (Peer Evaluatees)')
                ->options(function () {
                    $assignedEvaluateeIds = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->pluck('evaluatee_user_id')
                        ->toArray();

                    return $this->ownerRecord->users()
                        ->whereNotIn('users.id', $assignedEvaluateeIds)
                        ->pluck('name', 'users.id')
                        ->toArray();
                })
                ->multiple()
                ->searchable()
                ->placeholder('Select students for this peer evaluator')
                ->helperText('Only students without a peer evaluator are shown.'),
        ];
    }

    protected function assignPeerEvaluator(int $evaluateeId, int $evaluatorId): void
    {
        return [
            TextInput::make('position')
                ->label('Position')
                ->required()
                ->maxLength(255)
                ->prefixIcon('heroicon-m-identification'),

            Select::make('peer_evaluatee')
                ->label('Assign Students to Evaluate (Peer Evaluatees)')
                ->options(function ($record) {
                    $assignedEvaluateeIds = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                        ->pluck('evaluatee_user_id')
                        ->toArray();

                    return $this->ownerRecord->users()
                        ->where('users.id', '!=', $record->id)
                        ->whereNotIn('users.id', $assignedEvaluateeIds)
                        ->pluck('name', 'users.id')
                        ->toArray();
                })
                ->multiple()
                ->searchable()
                ->placeholder('Select students to evaluate')
                ->helperText('Only students without a peer evaluator are shown.')
        ];
    }

    protected function getStatusIcon(EvaluationPositionSlot $slot, string $type): string
    {
        if (!$slot->user_id) {
            return 'heroicon-o-minus';
        }

        return match ($this->getEvaluationStatus($slot->user_id, $type)) {
            'submitted' => 'heroicon-o-check-circle',
            'draft' => 'heroicon-o-pencil-square',
            default => 'heroicon-o-clock',
        };
    }

    protected function getStatusColor(EvaluationPositionSlot $slot, string $type): string
    {
        if (!$slot->user_id) {
            return 'gray';
        }

        return match ($this->getEvaluationStatus($slot->user_id, $type)) {
            'submitted' => 'success',
            'draft' => 'info',
            default => 'warning',
        };
    }

    protected function getEvaluationStatus(int $userId, string $evaluatorType): string
    {
        $status = EvaluationForm::where('evaluation_id', $this->ownerRecord->id)
            ->where('user_id', $userId)
            ->where('evaluator_type', $evaluatorType)
            ->value('status');
        return $status ?? 'pending';
    }

    protected function getUnassignedPeerEvaluatorCount(): int
    {
        $studentIds = EvaluationPositionSlot::where('evaluation_id', $this->ownerRecord->id)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique()
            ->toArray();

        if (empty($studentIds)) {
            return 0;
        }

        $assignedEvaluateeIds = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
            ->whereIn('evaluatee_user_id', $studentIds)
            ->pluck('evaluatee_user_id')
            ->toArray();

        return count(array_diff($studentIds, $assignedEvaluateeIds));
    }

    protected function assignPeerEvaluatee(int $evaluatorUserId, array|int|null $evaluateeId): void
    {
        try {
            $evaluateeIds = array_filter((array) $evaluateeId);

            if (empty($evaluateeIds)) {
                Notification::make()
                    ->title('Peer Evaluation Assignment Removed')
                    ->body('No peer evaluation assignment was added.')
                    ->info()
                    ->send();
                return;
            }

            $assignedCount = 0;
            $skippedSelf = false;
            $skippedAssigned = [];

            foreach ($evaluateeIds as $evaluateeIdValue) {
                if ($evaluateeIdValue === $evaluatorUserId) {
                    $skippedSelf = true;
                    continue;
                }

                $alreadyAssigned = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
                    ->where('evaluatee_user_id', $evaluateeIdValue)
                    ->exists();

                if ($alreadyAssigned) {
                    $skippedAssigned[] = $evaluateeIdValue;
                    continue;
                }

                EvaluationPeerEvaluator::create([
                    'evaluation_id' => $this->ownerRecord->id,
                    'evaluatee_user_id' => $evaluateeIdValue,
                    'evaluator_user_id' => $evaluatorUserId,
                    'assigned_by_user_id' => auth()->id(),
                    'assigned_at' => now(),
                ]);

                $assignedCount++;
            }

            if ($assignedCount > 0) {
                $evaluatorName = User::find($evaluatorUserId)->name;

                $evaluator = User::find($evaluatorUserId);
                if ($evaluator) {
                    Notification::make()
                        ->title('Peer Evaluations Assigned')
                        ->body("You were assigned to evaluate {$assignedCount} student(s) in {$this->ownerRecord->council->name} ({$this->ownerRecord->academic_year}).")
                        ->info()
                        ->sendToDatabase($evaluator);
                }

                Notification::make()
                    ->title('Peer Evaluators Assigned Successfully')
                    ->body("{$evaluatorName} assigned to evaluate {$assignedCount} student(s).")
                    ->success()
                    ->send();
            }

            if ($skippedSelf) {
                Notification::make()
                    ->title('Invalid Peer Assignment')
                    ->body('A student cannot evaluate themselves as a peer.')
                    ->warning()
                    ->send();
            }

            if (!empty($skippedAssigned)) {
                Notification::make()
                    ->title('Peer Evaluators Skipped')
                    ->body('Some students already have peer evaluators assigned and were skipped.')
                    ->info()
                    ->send();
            }
                
        } catch (\Exception $e) {
            Notification::make()
                ->title($title)
                ->body($body)
                ->success()
                ->sendToDatabase($user);
        }
    }

    protected function resolveEvaluationTypeForUser(EvaluationPositionSlot $record, User $user): ?string
    {
        if ($this->isCouncilAdviser()) {
            return 'adviser';
        }

        if ($user->role !== 'student') {
            return null;
        }

        if ($user->id === $record->user_id) {
            return 'self';
        }

        $isPeerEvaluator = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
            ->where('evaluator_user_id', $user->id)
            ->where('evaluatee_user_id', $record->user_id)
            ->exists();

        return $isPeerEvaluator ? 'peer' : null;
    }

    protected function getStatusNotificationTitle(): string
    {
        return match ($this->ownerRecord->status) {
            Evaluation::STATUS_ONGOING => 'Evaluation Opened',
            Evaluation::STATUS_COMPLETED => 'Evaluation Completed',
            default => 'Evaluation Closed',
        };
    }

    protected function getStatusNotificationBody(): string
    {
        return match ($this->ownerRecord->status) {
            Evaluation::STATUS_ONGOING => 'Students can now start their evaluations.',
            Evaluation::STATUS_COMPLETED => 'Evaluations are now read-only.',
            default => 'Evaluations are currently closed.',
        };
    }
}
