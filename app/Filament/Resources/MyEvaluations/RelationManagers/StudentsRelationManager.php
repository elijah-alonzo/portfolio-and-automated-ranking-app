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
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

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
        $columns = [
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
                                : asset('storage/'.$record->user->pfp))
                            : 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
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
                    ->color(fn (EvaluationPositionSlot $record) => $this->getStatusColor($record, 'self'))
                    ->tooltip('Click to view self evaluation form')
                    ->url(fn (EvaluationPositionSlot $record) => $this->canViewCriteriaForms()
                        && (bool) $record->user_id
                        && $this->getEvaluationStatus($record->user_id, 'self') !== 'pending'
                        ? $this->ownerRecord->getEvaluationUrl((int) $record->user_id, 'self')
                        : null),
                IconColumn::make('peer_status')
                    ->label('Peer')
                    ->state(fn () => true)
                    ->icon(fn (EvaluationPositionSlot $record) => $this->getStatusIcon($record, 'peer'))
                    ->color(fn (EvaluationPositionSlot $record) => $this->getStatusColor($record, 'peer'))
                    ->tooltip('Click to view peer evaluation form')
                    ->url(fn (EvaluationPositionSlot $record) => $this->canViewCriteriaForms()
                        && (bool) $record->user_id
                        && $this->getEvaluationStatus($record->user_id, 'peer') !== 'pending'
                        ? $this->ownerRecord->getEvaluationUrl((int) $record->user_id, 'peer')
                        : null),
                IconColumn::make('adviser_status')
                    ->label('Adviser')
                    ->state(fn () => true)
                    ->icon(fn (EvaluationPositionSlot $record) => $this->getStatusIcon($record, 'adviser'))
                    ->color(fn (EvaluationPositionSlot $record) => $this->getStatusColor($record, 'adviser'))
                    ->tooltip('Click to view adviser evaluation form')
                    ->url(fn (EvaluationPositionSlot $record) => $this->canViewCriteriaForms()
                        && (bool) $record->user_id
                        && $this->getEvaluationStatus($record->user_id, 'adviser') !== 'pending'
                        ? $this->ownerRecord->getEvaluationUrl((int) $record->user_id, 'adviser')
                        : null),
            ]),
        ];

        return $columns;
    }

    protected function canViewCriteriaForms(): bool
    {
        $user = auth()->user();

        return $user !== null && $this->ownerRecord->council_adviser_id === $user->id;
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
        if (! $this->isCouncilAdviser()) {
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
                ->requiresConfirmation()
                ->disabled(fn () => $this->isCompletedStage())
                ->action(function () {
                    $previousStatus = $this->ownerRecord->status;

                    if ($this->isClosedStage()) {
                        $unassignedCount = $this->getUnassignedPeerEvaluatorCount();
                        if ($unassignedCount > 0) {
                            Notification::make()
                                ->title('Peer Evaluators Required')
                                ->body("{$unassignedCount} student(s) do not have a peer evaluator assigned.")
                                ->warning()
                                ->send();

                            return;
                        }
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
                    Select::make('student_id')
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
                    Select::make('evaluator_id')
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
                if (! $user || ! $record->user_id) {
                    return 'Evaluate';
                }

                $type = $this->resolveEvaluationTypeForUser($record, $user);
                if (! $type) {
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
                if (! $user || ! $record->user_id) {
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
                if (! $user || ! $record->user_id) {
                    return true;
                }

                if (! $this->isOngoingStage()) {
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

                    return ! $isPeerEvaluator;
                }

                return true;
            });

        $actions[] = Action::make('remind')
            ->label('Remind')
            ->icon('heroicon-o-envelope')
            ->color('info')
            ->visible(fn (EvaluationPositionSlot $record) => $this->isOngoingStage()
                && $this->isCouncilAdviser()
                && (bool) $record->user_id
                && $this->hasPendingReminders($record))
            ->action(function (EvaluationPositionSlot $record) {
                $targets = $this->getPendingReminderTargets($record);

                if ($targets === []) {
                    Notification::make()
                        ->title('No Pending Evaluations')
                        ->body('All assigned evaluations for this student are already submitted.')
                        ->info()
                        ->send();

                    return;
                }

                $evaluationTitle = ($this->ownerRecord->council?->name ?? 'Council Evaluation')
                    .' '.$this->ownerRecord->academic_year;

                foreach ($targets as $target) {
                    if (blank($target['user']?->email)) {
                        continue;
                    }

                    $recipientName = $target['user']?->name ?? 'Student';

                    $message = <<<EOT
                        Greetings {$recipientName}!

                        This is a reminder that you have a pending {$target['type']} evaluation for {$target['evaluatee_name']} in {$evaluationTitle}.

                        Please submit it as soon as possible.
                        EOT;
                    Mail::raw($message, function ($mail) use ($target, $evaluationTitle) {
                        $mail->to($target['user']->email)
                            ->subject("Pending {$target['type']} evaluation - {$evaluationTitle}");
                    });
                }

                Notification::make()
                    ->title('Reminder Sent')
                    ->body('Email reminders were sent to pending evaluators.')
                    ->success()
                    ->send();
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
                ->body(($student?->name ?? 'A student').' was added to your evaluation.')
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
        $studentId = $slot->user_id;
        if (! $studentId) {
            return;
        }

        $slot->update(['user_id' => null]);

        $remainingSlots = EvaluationPositionSlot::where('evaluation_id', $this->ownerRecord->id)
            ->where('user_id', $studentId)
            ->exists();

        if (! $remainingSlots) {
            $this->ownerRecord->users()->detach($studentId);
        }

        EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
            ->where(function ($query) use ($studentId) {
                $query->where('evaluatee_user_id', $studentId)
                    ->orWhere('evaluator_user_id', $studentId);
            })
            ->delete();

        if (! $silent) {
            Notification::make()
                ->title('Student Removed')
                ->body('Student removed from the slot.')
                ->info()
                ->send();
        }
    }

    protected function assignPeerEvaluator(int $evaluateeId, int $evaluatorId): void
    {
        if ($evaluateeId === $evaluatorId) {
            Notification::make()
                ->title('Invalid Assignment')
                ->body('A student cannot evaluate themselves.')
                ->warning()
                ->send();

            return;
        }

        EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
            ->where('evaluatee_user_id', $evaluateeId)
            ->delete();

        EvaluationPeerEvaluator::create([
            'evaluation_id' => $this->ownerRecord->id,
            'evaluatee_user_id' => $evaluateeId,
            'evaluator_user_id' => $evaluatorId,
            'assigned_by_user_id' => auth()->id(),
            'assigned_at' => now(),
        ]);

        $evaluateeName = User::find($evaluateeId)?->name ?? 'Student';
        $evaluatorName = User::find($evaluatorId)?->name ?? 'Student';

        Notification::make()
            ->title('Peer Evaluator Assigned')
            ->body("{$evaluatorName} will evaluate {$evaluateeName}.")
            ->success()
            ->send();
    }

    protected function hasPendingReminders(EvaluationPositionSlot $record): bool
    {
        return $this->getPendingReminderTargets($record) !== [];
    }

    /**
     * @return array<int, array{user: ?User, type: string, evaluatee_name: string}>
     */
    protected function getPendingReminderTargets(EvaluationPositionSlot $record): array
    {
        $targets = [];
        $evaluatee = $record->user;
        if (! $evaluatee) {
            return [];
        }

        $evaluateeName = $evaluatee->name ?? 'Student';

        $selfStatus = $this->getEvaluationStatus($evaluatee->id, 'self');
        if ($selfStatus !== 'submitted') {
            $targets[$evaluatee->id.'-self'] = [
                'user' => $evaluatee,
                'type' => 'self',
                'evaluatee_name' => $evaluateeName,
            ];
        }

        $peerAssignment = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
            ->where('evaluatee_user_id', $evaluatee->id)
            ->first();

        if ($peerAssignment) {
            $peerStatus = $this->getEvaluationStatus($evaluatee->id, 'peer');
            if ($peerStatus !== 'submitted') {
                $peerEvaluator = User::find($peerAssignment->evaluator_user_id);
                if ($peerEvaluator) {
                    $targets[$peerEvaluator->id.'-peer'] = [
                        'user' => $peerEvaluator,
                        'type' => 'peer',
                        'evaluatee_name' => $evaluateeName,
                    ];
                }
            }
        }

        return array_values($targets);
    }

    protected function getEligibleStudentOptions(EvaluationPositionSlot $slot): array
    {
        $allowedDepartmentIds = $this->getAllowedDepartmentIds();
        $assignedStudentIds = EvaluationPositionSlot::where('evaluation_id', $this->ownerRecord->id)
            ->whereNotNull('user_id')
            ->where('id', '!=', $slot->id)
            ->pluck('user_id')
            ->toArray();

        return User::where('role', 'student')
            ->when($allowedDepartmentIds, fn ($query) => $query->whereIn('department_id', $allowedDepartmentIds))
            ->whereNotIn('id', $assignedStudentIds)
            ->pluck('name', 'id')
            ->toArray();
    }

    protected function getEligiblePeerEvaluatorOptions(int $evaluateeId): array
    {
        $studentIds = EvaluationPositionSlot::where('evaluation_id', $this->ownerRecord->id)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique()
            ->toArray();

        $studentIds = array_values(array_diff($studentIds, [$evaluateeId]));

        return User::whereIn('id', $studentIds)->pluck('name', 'id')->toArray();
    }

    protected function isStudentAlreadyAssigned(int $studentId, ?int $ignoreSlotId = null): bool
    {
        return EvaluationPositionSlot::where('evaluation_id', $this->ownerRecord->id)
            ->when($ignoreSlotId, fn ($query) => $query->where('id', '!=', $ignoreSlotId))
            ->where('user_id', $studentId)
            ->exists();
    }

    protected function getAllowedDepartmentIds(): array
    {
        $council = $this->ownerRecord->council;
        if (! $council) {
            return [];
        }

        return $council->departments()->pluck('departments.id')->toArray();
    }

    protected function getPositionSlotLabel(EvaluationPositionSlot $slot): string
    {
        return $slot->position?->title ?? 'Position';
    }

    protected function getRecommendationForPosition(?string $positionTitle): ?string
    {
        if (! $positionTitle) {
            return null;
        }

        $branch = Position::where('title', $positionTitle)->value('branch');

        return match ($branch) {
            'Executive' => 'Recommended: Executive (3.00-2.41)',
            'Legislative' => 'Recommended: Legislative (2.40-1.81)',
            'Judiciary', 'Mayoral' => 'Recommended: Judicial/Mayoral (1.80-1.21)',
            default => null,
        };
    }

    protected function getPeerEvaluatorName(EvaluationPositionSlot $slot): string
    {
        if (! $slot->user_id) {
            return 'Unassigned';
        }

        $evaluatorId = EvaluationPeerEvaluator::where('evaluation_id', $this->ownerRecord->id)
            ->where('evaluatee_user_id', $slot->user_id)
            ->value('evaluator_user_id');

        if (! $evaluatorId) {
            return 'Unassigned';
        }

        return User::where('id', $evaluatorId)->value('name') ?? 'Unassigned';
    }

    protected function getStatusIcon(EvaluationPositionSlot $slot, string $type): string
    {
        if (! $slot->user_id) {
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
        if (! $slot->user_id) {
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

    protected function isCouncilAdviser(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        return $this->ownerRecord->council_adviser_id === $user->id;
    }

    protected function isClosedStage(): bool
    {
        return $this->ownerRecord->status === Evaluation::STATUS_CLOSED;
    }

    protected function isOngoingStage(): bool
    {
        return $this->ownerRecord->status === Evaluation::STATUS_ONGOING;
    }

    protected function isCompletedStage(): bool
    {
        return $this->ownerRecord->status === Evaluation::STATUS_COMPLETED;
    }

    protected function notifyEvaluationStatusChange(string $previousStatus, string $currentStatus): void
    {
        if (! in_array($currentStatus, [Evaluation::STATUS_ONGOING, Evaluation::STATUS_COMPLETED], true)) {
            return;
        }

        if ($previousStatus === $currentStatus) {
            return;
        }

        $title = $currentStatus === Evaluation::STATUS_ONGOING
            ? 'Evaluation Opened'
            : 'Evaluation Completed';
        $body = $currentStatus === Evaluation::STATUS_ONGOING
            ? 'An evaluation you are part of is now open.'
            : 'An evaluation you are part of has been completed.';

        $users = $this->ownerRecord->users()->get();
        $adviser = $this->ownerRecord->adviser;

        if ($adviser) {
            $users->push($adviser);
        }

        $adminUsers = User::where('role', 'admin')->get();
        $users = $users->merge($adminUsers)->unique('id');

        foreach ($users as $user) {
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
