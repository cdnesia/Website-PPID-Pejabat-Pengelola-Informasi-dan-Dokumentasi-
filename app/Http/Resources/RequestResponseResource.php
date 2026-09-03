<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResponseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'response_text' => $this->response_text,
            'responded_at' => $this->responded_at,
            'admin' => $this->whenLoaded('admin', fn () => $this->admin->name),
            'files' => $this->getMedia()->map(fn ($media) => [
                'id' => $media->id,
                'name' => $media->name,
                'url' => route('media.show', $media),
            ]),
        ];
    }
}
