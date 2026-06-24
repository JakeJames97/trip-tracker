<?php

namespace App\Models;

use App\Enums\TripStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property Carbon $created_at
 */
class Trip extends Model
{
    use HasFactory, HasUuids;

    protected $perPage = 14;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => TripStatus::class,
            'is_public' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Destination, $this>
     */
    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class);
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'trip_likes');
    }

    #[Scope]
    protected function public(Builder $query): void
    {
        $query->where('is_public', true);
    }

    #[Scope]
    protected function planned(Builder $query): void
    {
        $query->where('status', TripStatus::PLANNED->value);
    }

    #[Scope]
    protected function completed(Builder $query): void
    {
        $query->where('status', TripStatus::COMPLETED->value);
    }

    #[Scope]
    protected function inProgress(Builder $query): void
    {
        $query->where('status', TripStatus::PROGRESS->value);
    }
}
