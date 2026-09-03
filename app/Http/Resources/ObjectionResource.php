<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ObjectionResource extends JsonResource
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
            'reason' => $this->reason,
            'status' => $this->status,
            'response_text' => $this->response_text,
            'responded_at' => $this->responded_at,
            'user' => $this->whenLoaded('user', fn () => $this->user->name),
            'request_number' => $this->whenLoaded('request', fn () => $this->request->request_number),
            'evidence' => $this->getMedia('evidence')->map(fn ($media) => [
                'id' => $media->id,
                'name' => $media->name,
                'url' => route('media.show', $media),
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
