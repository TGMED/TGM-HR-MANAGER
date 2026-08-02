<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $employee_id
 * @property string $name
 * @property string $email
 * @property Role $role
 * @property string|null $phone
 * @property string|null $department
 * @property string|null $position
 * @property Carbon|null $hired_at
 * @property bool $is_active
 * @property Carbon|null $deactivated_at
 * @property int|null $location_id
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Location|null $location
 */
#[Fillable([
    'employee_id',
    'name',
    'email',
    'password',
    'role',
    'phone',
    'department',
    'position',
    'hired_at',
    'is_active',
    'deactivated_at',
    'location_id',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $appends = ['initials'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'hired_at' => 'date',
            'is_active' => 'boolean',
            'deactivated_at' => 'datetime',
            'location_id' => 'integer',
        ];
    }

    /**
     * The site this person clocks in at.
     *
     * @return BelongsTo<Location, $this>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return HasMany<Attendance, $this>
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * @return HasMany<ClockAttempt, $this>
     */
    public function clockAttempts(): HasMany
    {
        return $this->hasMany(ClockAttempt::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === Role::SuperAdmin;
    }

    /**
     * Super admins administer the system rather than work a shift, so they
     * have no attendance of their own.
     */
    public function clocksIn(): bool
    {
        return ! $this->isSuperAdmin();
    }

    /**
     * Two-letter monogram used by the avatar component.
     */
    public function getInitialsAttribute(): string
    {
        $words = array_values(array_filter(preg_split('/\s+/', trim($this->name)) ?: []));

        $letters = array_map(
            fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)),
            array_slice($words, 0, 2),
        );

        return implode('', $letters) ?: '?';
    }

    /**
     * @param  Builder<User>  $query
     */
    public function scopeStaff(Builder $query): void
    {
        $query->where('role', Role::Staff->value);
    }

    /**
     * @param  Builder<User>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
