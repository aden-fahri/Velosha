<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_PESERTA = UserRole::PESERTA->value;

    public const ROLE_PENYELENGGARA = UserRole::PENYELENGGARA->value;

    public const ROLE_OPERATOR = UserRole::OPERATOR->value;

    public const ROLE_ADMIN = UserRole::ADMIN->value;

    public const ROLES = [
        self::ROLE_PESERTA,
        self::ROLE_PENYELENGGARA,
        self::ROLE_OPERATOR,
        self::ROLE_ADMIN,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'birth_date',
        'gender',
        'blood_type',
        'avatar',
        'provider',
        'provider_id',
        'role',
        'is_system',
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
            'birth_date' => 'date',
            'is_system' => 'boolean',
        ];
    }

    public function isSystem(): bool
    {
        return (bool) $this->is_system;
    }

    /**
     * Scope untuk mengambil operator yang dapat ditugaskan (bukan bot sistem).
     */
    public function scopeAssignableOperators($query)
    {
        return $query->whereIn('role', [self::ROLE_OPERATOR, self::ROLE_ADMIN])
            ->where('is_system', false);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isPenyelenggara(): bool
    {
        return $this->role === self::ROLE_PENYELENGGARA;
    }

    public function isOperator(): bool
    {
        return $this->role === self::ROLE_OPERATOR;
    }

    public function isPeserta(): bool
    {
        return $this->role === self::ROLE_PESERTA;
    }

    /**
     * Rute tujuan setelah login berdasar role.
     */
    public function home(): string
    {
        if ($this->isPeserta()) {
            return '/dashboard';
        }

        if ($this->isOperator()) {
            return '/helpdesk';
        }

        return '/admin';
    }

    /**
     * Riwayat pendaftaran race milik pengguna ini.
     *
     * @return HasMany<Registration, $this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Apakah role ini boleh membuka URL tersebut?
     * Dipakai untuk memvalidasi URL intended sebelum redirect pasca-login.
     */
    public function canVisit(?string $url): bool
    {
        if (blank($url)) {
            return false;
        }

        $path = parse_url($url, PHP_URL_PATH) ?: '/';

        if (str_starts_with($path, '/admin')) {
            return $this->isAdmin() || $this->isPenyelenggara();
        }

        if (str_starts_with($path, '/helpdesk')) {
            return $this->isOperator() || $this->isAdmin();
        }

        // Halaman khusus peserta: portal rider & formulir pendaftaran race.
        if (in_array($path, ['/dashboard', '/checkout'], true)) {
            return $this->isPeserta();
        }

        // Halaman publik (/, /events, /ticket, dst.) bebas diakses siapa pun.
        return true;
    }

    /**
     * URL tujuan setelah auth: hormati intended bila role-nya valid,
     * selain itu jatuhkan ke home() role.
     */
    public function landingUrl(): string
    {
        $intended = session()->pull('url.intended');

        return $this->canVisit($intended) ? $intended : $this->home();
    }

    /**
     * Hanya admin & penyelenggara yang boleh masuk panel Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_PENYELENGGARA], true);
    }
}
