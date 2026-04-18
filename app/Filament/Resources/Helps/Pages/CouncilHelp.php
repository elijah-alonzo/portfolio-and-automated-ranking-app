<?php

namespace App\Filament\Resources\Helps\Pages;

use App\Filament\Resources\Helps\HelpResource;

class CouncilHelp extends HelpPage
{
	protected static string $resource = HelpResource::class;

	protected static ?string $title = 'Councils';

	protected string $view = 'Help.Councils';
}
