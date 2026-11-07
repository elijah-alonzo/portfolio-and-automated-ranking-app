<?php

namespace App\Features\Users\Models;

use App\Features\Certificates\Models\Certificate;
use App\Features\Departments\Models\Department;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Models\EvaluationRank;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[UseFactory(UserFactory::class)]
class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'contact_number',
        'role',
        'department_id',
        'is_active',
        'bio',
        'password',
        'pfp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the evaluations where this user is the adviser.
     *
     * @return HasMany<Evaluation, $this>
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'council_adviser_id');
    }

    /**
     * Get the evaluations this user is participating in
     *
     * @return BelongsToMany<Evaluation, $this>
     */
    public function participatingEvaluations(): BelongsToMany
    {
        return $this->belongsToMany(Evaluation::class, 'evaluation_user')
            ->withPivot('position')
            ->withTimestamps();
    }

    /**
     * Get certificates uploaded by this user.
     *
     * @return HasMany<Certificate, $this>
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /** @return BelongsTo<Department, $this> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get evaluation ranks for this user.
     *
     * @return HasMany<EvaluationRank, $this>
     */
    public function evaluationRanks(): HasMany
    {
        return $this->hasMany(EvaluationRank::class);
    }

    /**
     * The admin panel is for admins only. Advisers and students work in the React app.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAdviser(): bool
    {
        return $this->role === 'adviser';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Profile photo URL, or null so the React app shows the initial. Root-relative, so it works on any host and port.
     */
    public function avatarUrl(): ?string
    {
        if (blank($this->pfp)) {
            return null;
        }

        $path = str_replace('\\', '/', $this->pfp);

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return '/storage/'.preg_replace('#^/?storage/#', '', ltrim($path, '/'));
    }

    /**
     * Photo URL with a fallback image of the person's initials, for places that always need an image (the admin panel).
     */
    public function avatarOrInitialsUrl(): string
    {
        return $this->avatarUrl()
            ?? 'https://ui-avatars.com/api/?name='.urlencode((string) $this->name).'&color=036635&background=E8F2EC&bold=true';
    }

    /**
     * The admin panel's user menu shows the same photo as the app.
     */
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatarOrInitialsUrl();
    }
}
