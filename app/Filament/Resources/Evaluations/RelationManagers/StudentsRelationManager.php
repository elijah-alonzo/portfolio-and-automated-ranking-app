<?php

namespace App\Filament\Resources\Evaluations\RelationManagers;

use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Models\EvaluationForm;
use App\Models\EvaluationRank;
use App\Models\Position;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ColumnGroup;
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
                    ->label('Picture')
                    ->circular()
                    ->size(40)
                    ->getStateUsing(function ($record) {
                        $name = $record->name ?? 'Unassigned';

                        return $record->pfp
                            ? (str_starts_with($record->pfp, 'http')
                                ? $record->pfp
                                : asset('storage/'.$record->pfp))
                            : 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
                    }),
                TextColumn::make('name')
                    ->label('Student')
                    ->weight('medium')
                    ->searchable()
                    ->description(fn ($record) => $record->department?->name ?? 'No department'),
                TextColumn::make('pivot.position')
                    ->label('Position')
                    ->placeholder('No position assigned')
                    ->description(fn ($record) => $this->getRecommendationForPosition($record->pivot->position ?? null)),
            ]),
            ColumnGroup::make('Evaluation Scores', [
                TextColumn::make('self_score')
                    ->label('Self')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'self'))
                    ->tooltip('Click to view self evaluation')
                    ->url(fn ($record) => $this->getEvaluationScore($record->id, 'self') !== '-'
                        ? $this->getAdminEvaluationUrl($record->id, 'self')
                        : null)
                    ->color(fn ($record) => $this->getEvaluationScore($record->id, 'self') !== '-' ? 'success' : 'gray'),
                TextColumn::make('peer_score')
                    ->label('Peer')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'peer'))
                    ->tooltip('Click to view peer evaluation')
                    ->url(fn ($record) => $this->getEvaluationScore($record->id, 'peer') !== '-'
                        ? $this->getAdminEvaluationUrl($record->id, 'peer')
                        : null)
                    ->color(fn ($record) => $this->getEvaluationScore($record->id, 'peer') !== '-' ? 'success' : 'gray'),
                TextColumn::make('adviser_score')
                    ->label('Adviser')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'adviser'))
                    ->tooltip('Click to view adviser evaluation')
                    ->url(fn ($record) => $this->getEvaluationScore($record->id, 'adviser') !== '-'
                        ? $this->getAdminEvaluationUrl($record->id, 'adviser')
                        : null)
                    ->color(fn ($record) => $this->getEvaluationScore($record->id, 'adviser') !== '-' ? 'success' : 'gray'),
                TextColumn::make('total_score')
                    ->label('Total')
                    ->color('warning')
                    ->getStateUsing(fn ($record) => $this->getEvaluationRankValue($record->id, 'final_score'))
                    ->tooltip('Weighted total score'),
                TextColumn::make('rank')
                    ->label('Rank')
                    ->color('warning')
                    ->getStateUsing(fn ($record) => $this->getEvaluationRankValue($record->id, 'rank_display'))
                    ->tooltip('Final rank'),
            ]),
        ];
    }

    protected function getEvaluationRankValue(int $userId, string $field): string
    {
        $rank = EvaluationRank::where('evaluation_id', $this->ownerRecord->id)
            ->where('user_id', $userId)
            ->first();
        if (! $rank) {
            return '-';
        }
        if ($field === 'final_score') {
            return $rank->final_score !== null ? number_format($rank->final_score, 2) : '-';
        }
        if ($field === 'rank_display') {
            return $rank->rank_display;
        }

        return $rank->$field ?? '-';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getTableActions(): array
    {
        return [];
    }

    protected function getEvaluationScore(int $userId, string $evaluatorType): string
    {
        $score = EvaluationForm::where('evaluation_id', $this->ownerRecord->id)
            ->where('user_id', $userId)
            ->where('evaluator_type', $evaluatorType)
            ->value('evaluator_score');

        return $score !== null ? number_format($score, 2) : '-';
    }

    protected function getAdminEvaluationUrl(int $userId, string $evaluatorType): string
    {
        return EvaluationResource::getUrl('view-evaluation-form', [
            'evaluation' => $this->ownerRecord->id,
            'user' => $userId,
            'type' => $evaluatorType,
        ]);
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
}
