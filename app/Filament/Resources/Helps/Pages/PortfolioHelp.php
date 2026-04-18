<?php

namespace App\Filament\Resources\Helps\Pages;

use App\Filament\Resources\Helps\HelpResource;

class PortfolioHelp extends HelpPage
{
	protected static string $resource = HelpResource::class;

	protected static ?string $title = 'Portfolio';

	protected string $view = 'Help.Portfolio';
}
