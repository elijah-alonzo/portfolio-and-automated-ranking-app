<?php

namespace App\Filament\Resources\Evaluations\RelationManagers;

use App\Models\EvaluationForm;
use App\Models\EvaluationPeerEvaluator;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Support\Enums\FontWeight;

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
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'self'))
                    ->tooltip('Self evaluation score'),
                TextColumn::make('peer_score')
                    ->label('Peer')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'peer'))
                    ->tooltip('Peer evaluation score'),
                TextColumn::make('adviser_score')
                    ->label('Adviser')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'adviser'))
                    ->tooltip('Adviser evaluation score'),
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