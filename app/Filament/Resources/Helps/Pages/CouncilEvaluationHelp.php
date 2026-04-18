<?php

namespace App\Filament\Resources\Helps\Pages;

use App\Filament\Resources\Helps\HelpResource;

class CouncilEvaluationHelp extends HelpPage
{
    protected static string $resource = HelpResource::class;

    protected static ?string $title = 'Council Evaluation';

    protected string $view = 'Help.CouncilEvaluation';
}
