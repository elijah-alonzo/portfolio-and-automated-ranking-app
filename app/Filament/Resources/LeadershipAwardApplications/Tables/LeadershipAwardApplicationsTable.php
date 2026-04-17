<?php

namespace App\Filament\Resources\LeadershipAwardApplications\Tables;

use App\Models\EvaluationRank;
use App\Models\LeadershipAwardApplication;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
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
                ColumnGroup::make('Student Information', [
                    ImageColumn::make('user.pfp')
                        ->label('Picture')
                        ->circular()
                        ->size(40)
                        ->getStateUsing(function ($record) {
                            $name = $record->user?->name ?? 'Student';

                            return $record->user?->pfp
                                ? (str_starts_with($record->user->pfp, 'http')
                                    ? $record->user->pfp
                                    : asset('storage/' . $record->user->pfp))
                                : 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=EBF4FF';
                        }),
                    TextColumn::make('user.name')
                        ->label('Student')
                        ->weight('medium')
                        ->searchable()
                        ->description(fn ($record) => $record->user?->department?->name ?? 'No department'),
                ]),
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
                ActionGroup::make([
                    Action::make('view_portfolio')
                        ->label('View Portfolio')
                        ->icon('heroicon-m-eye')
                        ->color('info')
                        ->url(fn (LeadershipAwardApplication $record): string =>
                            \App\Filament\Resources\LeadershipAwardApplications\LeadershipAwardApplicationResource::getUrl('portfolio', ['application' => $record->id])
                        ),
                    Action::make('accept')
                        ->label('Accept')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->action(function (LeadershipAwardApplication $record) {
                            $record->update(['status' => 'accepted']);
                            if ($record->user) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Award Application Accepted')
                                    ->body('Your leadership award application has been accepted.')
                                    ->success()
                                    ->sendToDatabase($record->user);
                            }
                        }),
                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->action(function (LeadershipAwardApplication $record) {
                            $record->update(['status' => 'rejected']);
                            if ($record->user) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Award Application Rejected')
                                    ->body('Your leadership award application has been rejected.')
                                    ->danger()
                                    ->sendToDatabase($record->user);
                            }
                        }),
                ])
                ->label('')
                ->icon('heroicon-m-ellipsis-vertical')
                ->iconButton(),
            ])
            
            ->defaultSort('created_at', 'desc');
    }
}