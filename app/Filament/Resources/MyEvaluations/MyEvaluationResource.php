<?php

namespace App\Filament\Resources\MyEvaluations;

use App\Filament\Resources\MyEvaluations\Pages\EvaluateStudentPage;
use App\Filament\Resources\MyEvaluations\Pages\ListMyEvaluations;
use App\Filament\Resources\MyEvaluations\Pages\ViewMyEvaluation;
use App\Filament\Resources\MyEvaluations\Tables\MyEvaluationsTable;
use App\Models\Evaluation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyEvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static ?string $modelLabel = 'My Council';

    protected static ?string $pluralModelLabel = 'My Councils';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()
            ->whereIn('status', ['closed', 'ongoing'])
            ->count();
    }

    protected static ?int $navigationSort = 1;

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return MyEvaluationsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['adviser', 'council']);

        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($q) use ($user) {
            $q->where('council_adviser_id', $user->id)
                ->orWhereHas('users', function ($subQ) use ($user) {
                    $subQ->where('user_id', $user->id);
                });
        });
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if (in_array($user->role, ['admin', 'adviser'])) {
            return $record->council_adviser_id === $user->id;
        }

        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMyEvaluations::route('/'),
            'view' => ViewMyEvaluation::route('/{record}'),
            'evaluate-student' => EvaluateStudentPage::route('/{evaluation}/evaluate/{user}/{type}'),
        ];
    }
}
