<?php

namespace App\Models;

use Database\Factories\RequestResponseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['request_id', 'admin_id', 'response_text', 'responded_at'])]
class RequestResponse extends Model implements HasMedia
{
    /** @use HasFactory<RequestResponseFactory> */
    use HasFactory, InteractsWithMedia;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('response')->useDisk('local');
    }

    /**
     * @return BelongsTo<InformationRequest, $this>
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(InformationRequest::class, 'request_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
