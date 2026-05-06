<?php

namespace App\Filament\Resources\Positions\Pages;

use App\Filament\Resources\Positions\PositionsResource;
use App\Models\CouncilPosition;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPositions extends EditRecord
{
    protected static string $resource = PositionsResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $councilIds = array_values(array_unique($data['council_ids'] ?? []));
        $title = trim($data['title'] ?? '');
        $branch = $data['branch'] ?? null;
        $isActive = $data['is_active'] ?? true;
        $maxSlots = $data['max_slots'] ?? 1;

        unset($data['council_ids'], $data['title'], $data['branch']);

        $record->title = $title;
        $record->branch = $branch;
        $record->is_active = $isActive;
        $record->save();

        foreach ($councilIds as $councilId) {
            CouncilPosition::updateOrCreate(
                ['council_id' => $councilId, 'position_id' => $record->id],
                [
                    'max_slots' => $maxSlots,
                    'is_active' => $isActive,
                ]
            );
        }

        CouncilPosition::where('position_id', $record->id)
            ->whereNotIn('council_id', $councilIds)
            ->delete();

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return PositionsResource::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
