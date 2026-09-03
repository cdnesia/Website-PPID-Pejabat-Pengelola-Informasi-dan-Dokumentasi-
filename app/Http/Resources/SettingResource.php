<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'org_name' => $this->org_name,
            'org_email' => $this->org_email,
            'org_phone' => $this->org_phone,
            'org_address' => $this->org_address,
            'response_deadline_days' => $this->response_deadline_days,
            'banner_text' => $this->banner_text,
            'banner_is_active' => $this->banner_is_active,
            'logo_url' => $this->getFirstMediaUrl('logo') ?: null,
        ];
    }
}
