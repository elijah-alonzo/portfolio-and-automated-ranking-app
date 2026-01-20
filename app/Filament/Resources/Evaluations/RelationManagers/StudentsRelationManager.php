<?php

namespace App\Filament\Resources\Evaluations\RelationManagers;

use App\Models\EvaluationPeerEvaluator;
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
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pivot.position')
                    ->label('Position')
                    ->placeholder('No position assigned'),
            ]),
            ColumnGroup::make('Evaluation Scores', [
                TextColumn::make('self_score')
                    ->label('Self')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'self')),
                TextColumn::make('peer_score')
                    ->label('Peer')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'peer')),
                TextColumn::make('adviser_score')
                    ->label('Adviser')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'adviser')),
            ]),
        ];
    }

    protected function getHeaderActions(): array
    {
        // This is now read-only for admin monitoring purposes
        // Student management should be done through MyEvaluations resource
        return [];
    }

    protected function getTableActions(): array
    {
        // This is now read-only for admin monitoring purposes
        // Student management should be done through MyEvaluations resource
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
}