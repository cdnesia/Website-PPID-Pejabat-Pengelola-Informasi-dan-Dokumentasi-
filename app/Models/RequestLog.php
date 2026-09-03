<?php

namespace App\Models;

use Database\Factories\RequestLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['request_id', 'user_id', 'action', 'description', 'old_status', 'new_status'])]
class RequestLog extends Model
{
    /** @use HasFactory<RequestLogFactory> */
    use HasFactory;

    const UPDATED_AT = null;

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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
