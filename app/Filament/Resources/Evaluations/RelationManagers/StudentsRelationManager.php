<?php

namespace App\Filament\Resources\Evaluations\RelationManagers;

use App\Filament\Resources\Evaluations\EvaluationResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ColumnGroup;

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
            ColumnGroup::make('Evaluation Scores', [
                TextColumn::make('self_score')
                    ->label('Self')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'self'))
                    ->tooltip('Click to view self evaluation')
                    ->url(fn ($record) => $this->getEvaluationScore($record->id, 'self') !== '-' 
                        ? $this->getAdminEvaluationUrl($record->id, 'self') 
                        : null)
                    ->color(fn ($record) => $this->getEvaluationScore($record->id, 'self') !== '-' ? 'info' : 'gray'),
                TextColumn::make('peer_score')
                    ->label('Peer')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'peer'))
                    ->tooltip('Click to view peer evaluation')
                    ->url(fn ($record) => $this->getEvaluationScore($record->id, 'peer') !== '-' 
                        ? $this->getAdminEvaluationUrl($record->id, 'peer') 
                        : null)
                    ->color(fn ($record) => $this->getEvaluationScore($record->id, 'peer') !== '-' ? 'info' : 'gray'),
                TextColumn::make('adviser_score')
                    ->label('Adviser')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'adviser'))
                    ->tooltip('Click to view adviser evaluation')
                    ->url(fn ($record) => $this->getEvaluationScore($record->id, 'adviser') !== '-' 
                        ? $this->getAdminEvaluationUrl($record->id, 'adviser') 
                        : null)
                    ->color(fn ($record) => $this->getEvaluationScore($record->id, 'adviser') !== '-' ? 'info' : 'grays'),
                TextColumn::make('total_score')
                    ->label('Total')
                    ->getStateUsing(fn ($record) => $this->getEvaluationRankValue($record->id, 'final_score'))
                    ->tooltip('Weighted total score'),
                TextColumn::make('rank')
                    ->label('Rank')
                    ->getStateUsing(fn ($record) => $this->getEvaluationRankValue($record->id, 'rank_display'))
                    ->tooltip('Final rank'),
            ]),
        ];
    }

    protected function getEvaluationRankValue(int $userId, string $field): string
    {
        $rank = \App\Models\EvaluationRank::where('evaluation_id', $this->ownerRecord->id)
            ->where('user_id', $userId)
            ->first();
        if (!$rank) {
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
        $score = \App\Models\EvaluationForm::where('evaluation_id', $this->ownerRecord->id)
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
}