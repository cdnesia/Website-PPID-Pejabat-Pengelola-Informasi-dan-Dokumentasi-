<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable([
    'org_name', 'org_email', 'org_phone', 'org_address',
    'response_deadline_days', 'banner_text', 'banner_is_active',
])]
class Setting extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'banner_is_active' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }
}
