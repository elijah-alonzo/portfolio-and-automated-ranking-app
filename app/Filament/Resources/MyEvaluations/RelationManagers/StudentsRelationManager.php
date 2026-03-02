<?php

namespace App\Filament\Resources\MyEvaluations\RelationManagers;

use App\Models\User;
use App\Models\EvaluationPeerEvaluator;
use App\Models\EvaluationForm as EvaluationFormModel;
use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Notifications\Notification;

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
                    ->searchable()
                    ->sortable(),
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

        return [
            AttachAction::make()
                ->label('Add Student')
                ->color('info')
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
                })
                ->after(function (AttachAction $action, array $data, $record) {
                    if (isset($data['peer_evaluatee']) && !empty($data['peer_evaluatee'])) {
                        $this->assignPeerEvaluatee($data['recordId'], $data['peer_evaluatee']);
                    }
                }),
        ];
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

        if ($isAdviser) {
            $actions[] = EditAction::make()
                ->color('info')
                ->form($this->getEditForm())
                ->action(function ($record, $data) {
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

        return $actions;
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
                
            TextInput::make('position')
                ->label('Position')
                ->required()
                ->maxLength(255)
                ->placeholder('e.g., President, Secretary, Member')
                ->prefixIcon('heroicon-m-identification'),

            Select::make('peer_evaluatee')
                ->label('Assign Student to Evaluate (Peer Evaluatee)')
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
                ->placeholder('Select one student for this peer evaluator')
                ->helperText('⚠️ Each student can only have ONE peer evaluator. Only unassigned students are shown.'),
        ];
    }

    protected function getEditForm(): array
    {
        return [
            TextInput::make('position')
                ->label('Position')
                ->required()
                ->maxLength(255)
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

    protected function getEvaluationScore(int $userId, string $evaluatorType): string
    {
        $score = EvaluationFormModel::where('evaluation_id', $this->ownerRecord->id)
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
        return $user && $this->ownerRecord->council_adviser_id === $user->id;
    }
}