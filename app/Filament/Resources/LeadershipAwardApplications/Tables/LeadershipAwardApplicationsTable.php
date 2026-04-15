<?php

namespace App\Filament\Resources\LeadershipAwardApplications\Tables;

use App\Models\LeadershipAwardApplication;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;

class LeadershipAwardApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('user.name')
                    ->label('Student Name')
                    ->searchable(),
                TextColumn::make('awardType.name')
                    ->label('Award Type')
                    ->searchable(),
                SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                    ])
                    ->selectablePlaceholder(false)
                    ->afterStateUpdated(function (LeadershipAwardApplication $record, string $state): void {
                        if (! in_array($state, ['accepted', 'rejected'], true)) {
                            return;
                        }

                        $student = $record->user;

                        if (! $student) {
                            return;
                        }

                        $awardName = $record->awardType?->name ?? 'leadership award';
                        $statusLabel = $state === 'accepted' ? 'approved' : 'rejected';

                        Notification::make()
                            ->title('Award Application Update')
                            ->body("Your {$awardName} application was {$statusLabel}.")
                            ->info()
                            ->sendToDatabase($student);
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Applied At'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                    ]),
                SelectFilter::make('award_type_id')
                    ->relationship('awardType', 'name')
                    ->label('Award Type'),
            ])
            ->actions([
                Action::make('view_portfolio')
                    ->label('View Portfolio')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->url(fn (LeadershipAwardApplication $record): string => 
                        \App\Filament\Resources\LeadershipAwardApplications\LeadershipAwardApplicationResource::getUrl('portfolio', ['application' => $record->id])
                    ),
                DeleteAction::make(),
            ])
            
            ->defaultSort('created_at', 'desc');
    }
}