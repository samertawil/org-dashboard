<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, \Laravel\Sanctum\HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'activation',
        'google_id',
        'avatar',
        'needs_password_reset',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function scopeSearchName(Builder  $query, string $value): Builder
    {
        if ($value) {
            $query->where('name', 'like', "%{$value}%");
        }
        return $query;
    }

    public function scopeSearchActivation(Builder  $query, $value): Builder
    {
        if (!is_null($value) && $value !== '') {
            $query->where('activation', $value);
        }
        return $query;
    }

    public function scopeSearchEmail(Builder  $query, string $value): Builder
    {
        if ($value) {
            $query->where('email', 'like', "%{$value}%");
        }
        return $query;
    }


    public function rolesRelation(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->id === 1; // Or check based on specific Role ID
    }

    public function selectAnyStudent(): bool
    {
        if (Gate::allows('select.any.student')) {
            return true;
        }
        return false;
    }

    public function teacher()
    {
        // teacher_id in teacher_student_group stores the user id
        return $this->hasMany(TeacherStudentGroup::class, 'teacher_id', 'id');
    }
}
