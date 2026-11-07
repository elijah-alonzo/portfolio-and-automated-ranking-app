<?php

namespace App\Features\Users\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Notifications\DatabaseNotification;

/**
 * A database notification as the header bell shows it. Notifications are written by
 * Filament's notification builder, so the text is in `title` and `body`.
 *
 * @mixin DatabaseNotification
 */
class NotificationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->data['title'] ?? '',
            'message' => $this->data['body'] ?? '',
            'tone' => match ($this->data['status'] ?? null) {
                'success' => 'success',
                'danger' => 'danger',
                'warning' => 'action',
                default => 'info',
            },
            'read' => $this->read_at !== null,
            'at' => $this->created_at?->toIso8601String(),
        ];
    }
}
