<?php

namespace App\Filament\Resources\Helps\Pages;

use App\Filament\Resources\Helps\HelpResource;

class AwardHelp extends HelpPage
{
	protected static string $resource = HelpResource::class;

	protected static ?string $title = 'Awards';

	protected string $view = 'Help.Awards';
}
