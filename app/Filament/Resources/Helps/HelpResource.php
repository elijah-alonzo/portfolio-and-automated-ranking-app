<?php

namespace App\Filament\Resources\Helps;

use App\Filament\Resources\Helps\Pages\ApplicationHelp;
use App\Filament\Resources\Helps\Pages\AwardHelp;
use App\Filament\Resources\Helps\Pages\CertificateHelp;
use App\Filament\Resources\Helps\Pages\CouncilEvaluationHelp;
use App\Filament\Resources\Helps\Pages\CouncilHelp;
use App\Filament\Resources\Helps\Pages\DepartmentHelp;
use App\Filament\Resources\Helps\Pages\MyEvaluationHelp;
use App\Filament\Resources\Helps\Pages\OverviewHelp;
use App\Filament\Resources\Helps\Pages\PositionHelp;
use App\Filament\Resources\Helps\Pages\UserHelp;
use App\Models\Help;
use BackedEnum;
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
            'my-evaluation' => MyEvaluationHelp::route('/my-evaluation'),
            'users' => UserHelp::route('/users'),
            'departments' => DepartmentHelp::route('/departments'),
            'councils' => CouncilHelp::route('/councils'),
            'council-evaluation' => CouncilEvaluationHelp::route('/council-evaluation'),
            'positions' => PositionHelp::route('/positions'),
            'certificates' => CertificateHelp::route('/certificates'),
            'awards' => AwardHelp::route('/awards'),
            'applications' => ApplicationHelp::route('/applications'),
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
            NavigationItem::make('My Evaluations')
                ->icon('heroicon-o-clipboard-document-list')
                ->url(MyEvaluationHelp::getUrl())
                ->isActiveWhen(fn (): bool => request()->routeIs(MyEvaluationHelp::getRouteName()))
                ->sort(2),
            NavigationItem::make('Users')
                ->icon('heroicon-o-user-circle')
                ->url(UserHelp::getUrl())
                ->isActiveWhen(fn (): bool => request()->routeIs(UserHelp::getRouteName()))
                ->sort(3),
            NavigationItem::make('Departments')
                ->icon('heroicon-o-building-office-2')
                ->url(DepartmentHelp::getUrl())
                ->isActiveWhen(fn (): bool => request()->routeIs(DepartmentHelp::getRouteName()))
                ->sort(4),
            NavigationItem::make('Available Councils')
                ->icon('heroicon-o-user-group')
                ->url(CouncilHelp::getUrl())
                ->isActiveWhen(fn (): bool => request()->routeIs(CouncilHelp::getRouteName()))
                ->sort(5),
            NavigationItem::make('Council Evaluation')
                ->icon('heroicon-o-clipboard-document-check')
                ->url(CouncilEvaluationHelp::getUrl())
                ->isActiveWhen(fn (): bool => request()->routeIs(CouncilEvaluationHelp::getRouteName()))
                ->sort(6),
            NavigationItem::make('Council Positions')
                ->icon('heroicon-o-identification')
                ->url(PositionHelp::getUrl())
                ->isActiveWhen(fn (): bool => request()->routeIs(PositionHelp::getRouteName()))
                ->sort(7),
            NavigationItem::make('Issue Certificates')
                ->icon('heroicon-o-document-text')
                ->url(CertificateHelp::getUrl())
                ->isActiveWhen(fn (): bool => request()->routeIs(CertificateHelp::getRouteName()))
                ->sort(8),
            NavigationItem::make('Award Types')
                ->icon('heroicon-o-trophy')
                ->url(AwardHelp::getUrl())
                ->isActiveWhen(fn (): bool => request()->routeIs(AwardHelp::getRouteName()))
                ->sort(9),
            NavigationItem::make('Award Applications')
                ->icon('heroicon-o-envelope-open')
                ->url(ApplicationHelp::getUrl())
                ->isActiveWhen(fn (): bool => request()->routeIs(ApplicationHelp::getRouteName()))
                ->sort(10),
        ];
    }
}
