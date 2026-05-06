<?php

namespace App\Filament\Resources\Positions\Pages;

use App\Filament\Resources\Positions\PositionsResource;
use App\Models\CouncilPosition;
use App\Models\Position;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePositions extends CreateRecord
{
    protected static string $resource = PositionsResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $councilIds = $data['council_ids'] ?? [];
        $title = trim($data['title'] ?? '');
        $branch = $data['branch'] ?? null;
        $isActive = $data['is_active'] ?? true;

        unset($data['council_ids'], $data['title'], $data['branch']);

        $position = Position::create([
            'title' => $title,
            'branch' => $branch,
            'is_active' => $isActive,
        ]);

        foreach ($councilIds as $councilId) {
            CouncilPosition::create([
                'council_id' => $councilId,
                'position_id' => $position->id,
                'max_slots' => $data['max_slots'] ?? 1,
                'is_active' => $isActive,
            ]);
        }

        return $position;
    }

    protected function getRedirectUrl(): string
    {
        return PositionsResource::getUrl('index');
    }
}
