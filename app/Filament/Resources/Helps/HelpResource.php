<?php

namespace App\Filament\Resources\Helps;

use App\Filament\Resources\Helps\Pages\AdminHelp;
use App\Filament\Resources\Helps\Pages\AdviserHelp;
use App\Filament\Resources\Helps\Pages\OverviewHelp;
use App\Filament\Resources\Helps\Pages\StudentHelp;
use App\Models\Help;
use BackedEnum;
use UnitEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class HelpResource extends Resource
{
    protected static ?string $model = Help::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?int $navigationSort = 3;
    
    protected static ?string $modelLabel = 'Help';           

    protected static ?string $pluralModelLabel = 'Help';    

    public static function getNavigationLabel(): string
    {
        return 'Help';
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
            'index' => OverviewHelp::route('/'),
            'student' => StudentHelp::route('/student'),
            'adviser' => AdviserHelp::route('/adviser'),
            'admin' => AdminHelp::route('/admin'),
        ];
    }

    /**
     * @return array<NavigationItem>
     */
    public static function getSubNavigation(Page $page): array
    {
        return [
            NavigationItem::make('Introduction')
                ->icon('heroicon-o-bookmark')
                ->url(OverviewHelp::getUrl())
                ->isActiveWhen(fn (): bool => request()->routeIs(OverviewHelp::getRouteName()))
                ->sort(1),
            NavigationItem::make('Student')
                ->icon('heroicon-o-user')
                ->url(StudentHelp::getUrl())
                ->isActiveWhen(fn (): bool => request()->routeIs(StudentHelp::getRouteName()))
                ->visible(fn (): bool => self::userHasRole(['student', 'admin']))
                ->sort(2),
            NavigationItem::make('Adviser')
                ->icon('heroicon-o-academic-cap')
                ->url(AdviserHelp::getUrl())
                ->isActiveWhen(fn (): bool => request()->routeIs(AdviserHelp::getRouteName()))
                ->visible(fn (): bool => self::userHasRole(['adviser', 'admin']))
                ->sort(3),
            NavigationItem::make('Admin')
                ->icon('heroicon-o-shield-check')
                ->url(AdminHelp::getUrl())
                ->isActiveWhen(fn (): bool => request()->routeIs(AdminHelp::getRouteName()))
                ->visible(fn (): bool => self::userHasRole(['admin']))
                ->sort(4),
        ];
    }

    protected static function userHasRole(array $roles): bool
    {
        $role = auth()->user()?->role;

        return $role !== null && in_array($role, $roles, true);
    }
}
