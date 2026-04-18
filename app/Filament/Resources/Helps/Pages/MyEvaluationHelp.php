<?php

namespace App\Filament\Resources\Helps\Pages;

use App\Filament\Resources\Helps\HelpResource;

class MyEvaluationHelp extends HelpPage
{
    protected static string $resource = HelpResource::class;

    protected static ?string $title = 'My Evaluation';

    protected string $view = 'Help.MyEvaluation';
}
