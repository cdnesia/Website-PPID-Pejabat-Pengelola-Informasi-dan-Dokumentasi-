<?php

namespace App\Models;

use Database\Factories\InformationRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Boot;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable([
    'user_id', 'applicant_name', 'applicant_nik', 'applicant_occupation',
    'applicant_phone', 'applicant_email', 'applicant_address',
    'purpose', 'information_detail', 'format_requested', 'delivery_method', 'response_delivery_method',
    'status', 'rejection_reason', 'due_date', 'assigned_to',
])]
class InformationRequest extends Model implements HasMedia
{
    /** @use HasFactory<InformationRequestFactory> */
    use HasFactory, InteractsWithMedia;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'request_number';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('ktp')->useDisk('local');
        $this->addMediaCollection('power_of_attorney')->useDisk('local');
    }

    #[Boot]
    protected static function bootRequestNumberGeneration(): void
    {
        static::creating(function (self $request): void {
            if ($request->request_number) {
                return;
            }

            $year = now()->year;

            $sequence = DB::transaction(function () use ($year) {
                $lastNumber = static::where('request_number', 'like', "PPID-{$year}-%")
                    ->lockForUpdate()
                    ->count();

                return $lastNumber + 1;
            });

            $request->request_number = sprintf('PPID-%d-%04d', $year, $sequence);
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * @return HasMany<RequestResponse, $this>
     */
    public function responses(): HasMany
    {
        return $this->hasMany(RequestResponse::class, 'request_id');
    }

    /**
     * @return HasMany<Objection, $this>
     */
    public function objections(): HasMany
    {
        return $this->hasMany(Objection::class, 'request_id');
    }

    /**
     * @return HasMany<RequestLog, $this>
     */
    public function logs(): HasMany
    {
        return $this->hasMany(RequestLog::class, 'request_id');
    }
}
