<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
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
            'excerpt' => $this->excerpt,
            'content' => $this->when($request->routeIs('*.show'), $this->content),
            'category' => $this->category,
            'published_at' => $this->published_at,
            'view_count' => $this->view_count,
            'author' => $this->whenLoaded('author', fn () => $this->author->name),
            'thumbnail' => $this->getFirstMediaUrl('thumbnail') ?: null,
        ];
    }
}
