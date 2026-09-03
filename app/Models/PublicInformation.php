<?php

namespace App\Models;

use Database\Factories\PublicInformationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(name: 'public_informations')]
#[Fillable([
    'category_id', 'work_unit_id', 'title', 'slug', 'description', 'content',
    'file_url', 'status', 'published_at', 'created_by',
])]
class PublicInformation extends Model
{
    /** @use HasFactory<PublicInformationFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsTo<InformationCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(InformationCategory::class, 'category_id');
    }

    /**
     * @return BelongsTo<WorkUnit, $this>
     */
    public function workUnit(): BelongsTo
    {
        return $this->belongsTo(WorkUnit::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
