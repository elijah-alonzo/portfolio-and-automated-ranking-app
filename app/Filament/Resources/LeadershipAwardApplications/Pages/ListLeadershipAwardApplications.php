<?php

namespace App\Filament\Resources\LeadershipAwardApplications\Pages;

use App\Filament\Resources\LeadershipAwardApplications\LeadershipAwardApplicationResource;
use Filament\Resources\Pages\ListRecords;

class ListLeadershipAwardApplications extends ListRecords
{
    protected static string $resource = LeadershipAwardApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action needed as applications come from student submissions
        ];
    }
}