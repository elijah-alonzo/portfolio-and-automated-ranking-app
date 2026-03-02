<?php

namespace App\Filament\Resources\LeadershipAwardApplications\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Select;

class LeadershipAwardApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->relationship('user', 'name')
                ->required(),
            Select::make('award_type_id')
                ->relationship('awardType', 'name')
                ->required(),
            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'accepted' => 'Accepted',
                    'rejected' => 'Rejected',
                ])
                ->required(),
        ]);
    }
}