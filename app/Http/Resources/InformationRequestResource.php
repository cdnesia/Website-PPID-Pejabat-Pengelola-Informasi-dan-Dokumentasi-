<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InformationRequestResource extends JsonResource
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
            'request_number' => $this->request_number,
            'applicant_name' => $this->applicant_name,
            'applicant_nik' => $this->applicant_nik,
            'applicant_occupation' => $this->applicant_occupation,
            'applicant_phone' => $this->applicant_phone,
            'applicant_email' => $this->applicant_email,
            'applicant_address' => $this->applicant_address,
            'purpose' => $this->purpose,
            'information_detail' => $this->information_detail,
            'format_requested' => $this->format_requested,
            'delivery_method' => $this->delivery_method,
            'response_delivery_method' => $this->response_delivery_method,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'due_date' => $this->due_date,
            'user' => new UserResource($this->whenLoaded('user')),
            'assigned_to' => new UserResource($this->whenLoaded('assignedTo')),
            'responses' => RequestResponseResource::collection($this->whenLoaded('responses')),
            'objections' => ObjectionResource::collection($this->whenLoaded('objections')),
            'ktp' => $this->getFirstMedia('ktp') ? ['url' => route('media.show', $this->getFirstMedia('ktp'))] : null,
            'power_of_attorney' => $this->getFirstMedia('power_of_attorney') ? ['url' => route('media.show', $this->getFirstMedia('power_of_attorney'))] : null,
            'created_at' => $this->created_at,
        ];
    }
}
