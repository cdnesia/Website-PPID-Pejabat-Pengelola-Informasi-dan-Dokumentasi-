<?php

namespace App\Models;

use Database\Factories\WorkUnitFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name', 'head_name', 'head_title', 'description', 'is_active'])]
class WorkUnit extends Model
{
    /** @use HasFactory<WorkUnitFactory> */
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
        return $this->hasMany(PublicInformation::class);
    }
}
