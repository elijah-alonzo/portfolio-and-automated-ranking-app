<?php

namespace App\Filament\Resources\MyEvaluations\Pages;

use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use App\Models\EvaluationPeerEvaluator;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewMyEvaluation extends ViewRecord
{
    protected static string $resource = MyEvaluationResource::class;

    protected static ?string $title = 'My Evaluations';
    
    protected function getHeaderActions(): array
    {
        return [];
    }
    
    protected function getPeerEvaluationActions($record, array $peerEvaluateeIds): array
    {
        $actions = [];

        $evaluatees = $record->users()
            ->whereIn('user_id', $peerEvaluateeIds)
            ->get();
            
        foreach ($evaluatees as $evaluatee) {
            $actions[] = Action::make("evaluate_peer_{$evaluatee->id}")
                ->label("Evaluate {$evaluatee->name}")
                ->icon('heroicon-o-clipboard-document-check')
                ->url(fn() => MyEvaluationResource::getUrl('evaluate-student', ['evaluation' => $record->id, 'user' => $evaluatee->id, 'type' => 'peer']))
                ->tooltip("Complete peer evaluation for {$evaluatee->name}");
        }
        
        return $actions;
    }
}
