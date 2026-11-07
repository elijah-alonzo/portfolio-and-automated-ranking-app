<?php

namespace App\Filament\Resources\Certificates\Pages;

use App\Features\Certificates\Models\Certificate;
use App\Features\Users\Models\User;
use App\Filament\Resources\Certificates\CertificateResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCertificate extends CreateRecord
{
    protected static string $resource = CertificateResource::class;

    protected array $issuedUserIds = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $userIds = $data['user_ids'] ?? [];
        if (! is_array($userIds)) {
            $userIds = [$userIds];
        }

        $userIds = array_values(array_filter($userIds));
        $this->issuedUserIds = $userIds;

        if (! empty($userIds)) {
            $data['user_id'] = $userIds[0];
        }

        unset($data['user_ids']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        if (! $record instanceof Certificate) {
            return;
        }

        $remainingUserIds = array_values(array_diff($this->issuedUserIds, [$record->user_id]));
        foreach ($remainingUserIds as $userId) {
            Certificate::create([
                'user_id' => $userId,
                'file_path' => $record->file_path,
                'certification_name' => $record->certification_name,
                'date_issued' => $record->date_issued,
            ]);
        }

        $allUserIds = $this->issuedUserIds ?: [$record->user_id];
        foreach ($allUserIds as $userId) {
            $student = User::find($userId);
            if ($student) {
                Notification::make()
                    ->title('Certificate Issued')
                    ->body('You have been issued a certificate: '.$record->certification_name)
                    ->success()
                    ->sendToDatabase($student);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
