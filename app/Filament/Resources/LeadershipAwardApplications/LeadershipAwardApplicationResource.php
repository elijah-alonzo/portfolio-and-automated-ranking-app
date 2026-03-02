<?php

namespace App\Filament\Resources\LeadershipAwardApplications;

use App\Filament\Resources\LeadershipAwardApplications\Pages\ListLeadershipAwardApplications;
use App\Filament\Resources\LeadershipAwardApplications\Pages\ViewApplicantPortfolio;
use App\Filament\Resources\LeadershipAwardApplications\Schemas\LeadershipAwardApplicationForm;
use App\Filament\Resources\LeadershipAwardApplications\Tables\LeadershipAwardApplicationsTable;
use App\Models\LeadershipAwardApplication;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class LeadershipAwardApplicationResource extends Resource
{
    protected static ?string $model = LeadershipAwardApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Award Applications';

    protected static ?string $modelLabel = 'Award Application';
    
    protected static ?string $pluralModelLabel = 'Award Applications';

    protected static UnitEnum|string|null $navigationGroup = 'Award Management';

    protected static ?int $navigationSort = 6;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->role === 'admin';
    }

    public static function form(Schema $schema): Schema
    {
        return LeadershipAwardApplicationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadershipAwardApplicationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeadershipAwardApplications::route('/'),
            'portfolio' => ViewApplicantPortfolio::route('/{application}/portfolio'),
        ];
    }
}