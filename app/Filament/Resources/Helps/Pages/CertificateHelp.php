<?php

namespace App\Filament\Resources\Helps\Pages;

use App\Filament\Resources\Helps\HelpResource;

class CertificateHelp extends HelpPage
{
	protected static string $resource = HelpResource::class;

	protected static ?string $title = 'Certificates';

	protected string $view = 'Help.Certificates';
}
