<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicInformationResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $this->when($request->routeIs('*.show'), $this->content),
            'status' => $this->status,
            'published_at' => $this->published_at,
            'view_count' => $this->view_count,
            'file_url' => $this->file_url,
            'category' => new InformationCategoryResource($this->whenLoaded('category')),
            'work_unit' => new WorkUnitResource($this->whenLoaded('workUnit')),
        ];
    }
}
