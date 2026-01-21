<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TableWidget extends BaseTableWidget
{
    protected static ?string $heading = 'Pending Evaluations';
    protected int|string|array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => \App\Models\Evaluation::query()->where('status', false))
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('council.name')->label('Council')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('adviser.name')->label('Adviser')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('academic_year')->label('Academic Year')->sortable(),
            ])
            ->filters([])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
