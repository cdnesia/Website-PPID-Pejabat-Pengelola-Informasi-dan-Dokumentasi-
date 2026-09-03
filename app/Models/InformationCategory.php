<?php

namespace App\Models;

use Database\Factories\InformationCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'type', 'description', 'icon', 'is_active'])]
class InformationCategory extends Model
{
    /** @use HasFactory<InformationCategoryFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<PublicInformation, $this>
     */
    public function publicInformations(): HasMany
    {
        return $this->hasMany(PublicInformation::class, 'category_id');
    }
}
