<?php

namespace App\Filament\Resources\LeadershipAwardApplications\Tables;

use App\Filament\Resources\LeadershipAwardApplications\LeadershipAwardApplicationResource;
use App\Models\EvaluationRank;
use App\Models\LeadershipAwardApplication;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class LeadershipAwardApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->heading('Leadership Award Applications')
            ->description('List of all leadership award applications submitted by students.')
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
                                    : asset('storage/'.$record->user->pfp))
                                : 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
                        }),
                    TextColumn::make('user.name')
                        ->label('Student')
                        ->weight('medium')
                        ->searchable()
                        ->description(fn ($record) => $record->user?->department?->name ?? 'No department'),
                ]),
                TextColumn::make('awardType.name')
                    ->label('Award Type')
                    ->searchable()
                    ->badge(),
                TextColumn::make('rank_support')
                    ->label('Rank')
                    ->getStateUsing(function (LeadershipAwardApplication $record) {
                        $rank = EvaluationRank::query()
                            ->where('user_id', $record->user_id)
                            ->whereHas('evaluation.council', function ($query) use ($record) {
                                $query->where('award_type_id', $record->award_type_id);
                            })
                            ->orderByDesc('evaluation_id')
                            ->first();

                        if (! $rank || ! $rank->rank_display) {
                            return 'N/A';
                        }

                        $score = $rank->final_score !== null
                            ? number_format($rank->final_score, 2)
                            : 'N/A';

                        return $rank->rank_display.' ('.$score.')';
                    })
                    ->badge(),
                IconColumn::make('status')
                    ->label('Status')
                    ->state(fn () => true)
                    ->icon(fn (LeadershipAwardApplication $record) => match ($record->status) {
                        'accepted' => 'heroicon-o-check-circle',
                        'rejected' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-clock',
                    })
                    ->color(fn (LeadershipAwardApplication $record) => match ($record->status) {
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
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
                        ->url(fn (LeadershipAwardApplication $record): string => LeadershipAwardApplicationResource::getUrl('portfolio', ['application' => $record->id])
                        ),
                    Action::make('accept')
                        ->label('Accept')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->action(function (LeadershipAwardApplication $record) {
                            $record->update(['status' => 'accepted']);

                            if ($record->user) {
                                Notification::make()
                                    ->title('Award Application Accepted')
                                    ->body('Your leadership award application has been accepted.')
                                    ->success()
                                    ->sendToDatabase($record->user);

                                if (filled($record->user->email)) {
                                    $studentName = $record->user->name ?? 'Student';
                                    $awardTypeName = $record->awardType?->name ?? 'Leadership Award';

                                    $message = <<<EOT
                                        Greetings {$studentName}!

                                        We are happy to inform you that your application for the {$awardTypeName} has been accepted! Please claim your award at the Student Affairs and Academic Services office to claim it.

                                        Sincerely,
                                        Paulinian Student Government
                                        EOT;

                                    Mail::raw($message, function ($mail) use ($record, $awardTypeName) {
                                        $mail->to($record->user->email)
                                            ->subject("Leadership Award Application Accepted - {$awardTypeName}");
                                    });
                                }
                            }

                            Notification::make()
                                ->title('Application Accepted')
                                ->body('The leadership award application was accepted successfully.')
                                ->success()
                                ->send();
                        }),
                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->action(function (LeadershipAwardApplication $record) {
                            $record->update(['status' => 'rejected']);

                            if ($record->user) {
                                Notification::make()
                                    ->title('Award Application Rejected')
                                    ->body('Your leadership award application has been rejected.')
                                    ->danger()
                                    ->sendToDatabase($record->user);

                                if (filled($record->user->email)) {
                                    $studentName = $record->user->name ?? 'Student';
                                    $awardTypeName = $record->awardType?->name ?? 'Leadership Award';

                                    $message = <<<EOT
Greetings {$studentName}!

We regret to inform you that your application for the {$awardTypeName} has been unsuccessful at this time. You are welcome to reapply once all necessary application requirements have been completed and submitted.

Sincerely,
Paulinian Student Government
EOT;

                                    Mail::raw($message, function ($mail) use ($record, $awardTypeName) {
                                        $mail->to($record->user->email)
                                            ->subject("Leadership Award Application Update - {$awardTypeName}");
                                    });
                                }
                            }

                            Notification::make()
                                ->title('Application Rejected')
                                ->body('The leadership award application was rejected successfully.')
                                ->danger()
                                ->send();
                        }),
                ])
                    ->label('')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->iconButton(),
            ])

            ->defaultSort('created_at', 'desc');
    }
}
