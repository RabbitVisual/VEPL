<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Bible\App\Traits\HasReadingProgress;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasReadingProgress, Notifiable;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['photo_url'];

    /**
     * Boot function from Laravel
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            // Ensure name is always synced from first_name + last_name if they are present
            if (! empty($user->first_name) && ! empty($user->last_name)) {
                $user->name = trim($user->first_name.' '.$user->last_name);
            }
            // Ensure first_name and last_name are synced from name if they are empty (Legacy support during save)
            elseif (! empty($user->name) && (empty($user->first_name) || empty($user->last_name))) {
                $parts = explode(' ', $user->name, 2);
                $user->first_name = $parts[0] ?? '';
                $user->last_name = $parts[1] ?? '';
            }
        });
    }

    /**
     * Acessor para first_name (Legacy Support)
     */
    protected function firstName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function (?string $value, array $attributes) {
                if (! empty($value)) {
                    return $value;
                }

                return explode(' ', $attributes['name'] ?? '', 2)[0] ?? '';
            },
        );
    }

    /**
     * Acessor para last_name (Legacy Support)
     */
    protected function lastName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function (?string $value, array $attributes) {
                if (! empty($value)) {
                    return $value;
                }

                return explode(' ', $attributes['name'] ?? '', 2)[1] ?? '';
            },
        );
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'cpf',
        'date_of_birth',
        'gender',
        'marital_status',
        'email',
        'phone',
        'cellphone',
        'email_verified_at',
        'address',
        'address_number',
        'address_complement',
        'neighborhood',
        'city',
        'state',
        'zip_code',
        'title',
        'is_ordained',
        'ordination_date',
        'ministry_start_date',
        'affiliated_church',
        'baptist_convention',
        'theological_education',
        'biography',
        'is_baptized',
        'baptism_date',
        'profession',
        'education_level',
        'workplace',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'password',
        'role_id',
        'is_active',
        'photo',
        'notes',
        'can_project',
        'two_factor_secret',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
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
            'date_of_birth' => 'date',
            'ordination_date' => 'date',
            'ministry_start_date' => 'date',
            'baptism_date' => 'date',
            'is_ordained' => 'boolean',
            'is_baptized' => 'boolean',
            'is_active' => 'boolean',
            'can_project' => 'boolean',
            'two_factor_secret' => 'encrypted',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Verifica se o usuário tem 2FA (TOTP) ativo e confirmado.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return ! empty($this->two_factor_secret) && $this->two_factor_confirmed_at !== null;
    }

    /**
     * Relacionamento com Role
     */
    public function role()
    {
        return $this->belongsTo(\App\Models\Role::class);
    }

    /**
     * Relacionamento com Ministérios
     */
    public function ministries()
    {
        return $this->belongsToMany(
            \Modules\Ministries\App\Models\Ministry::class,
            'ministry_members'
        )->withPivot('role', 'status', 'joined_at', 'approved_at', 'approved_by', 'notes')
            ->withTimestamps();
    }

    public function bibleFavorites()
    {
        return $this->belongsToMany(
            \Modules\Bible\App\Models\Verse::class,
            'bible_favorites',
            'user_id',
            'verse_id'
        )->withPivot('color')->withTimestamps();
    }

    /**
     * Relacionamento com fotos de perfil
     */
    public function profilePhotos()
    {
        return $this->hasMany(UserPhoto::class);
    }

    /**
     * Vínculos familiares (parentesco): quem este usuário declarou como pai, mãe, cônjuge, etc.
     */
    public function relationships()
    {
        return $this->hasMany(UserRelationship::class, 'user_id');
    }

    /**
     * Quantidade de vínculos familiares (accepted + pending) para exibição na listagem.
     */
    public function getFamilyCount(): int
    {
        return $this->relationships()->count();
    }

    /**
     * Relacionamento com Ministérios ativos
     */
    public function activeMinistries()
    {
        return $this->ministries()->wherePivot('status', 'active');
    }

    /**
     * Verifica se o usuário pode projetar
     */
    public function canProject()
    {
        return $this->can_project || $this->isAdmin() || $this->isPastor();
    }

    /**
     * Verifica se o usuário é admin
     */
    public function isAdmin()
    {
        return $this->role && $this->role->slug === 'admin';
    }

    /**
     * Verifica se o usuário é membro
     */
    public function isMember()
    {
        return $this->role && $this->role->slug === 'membro';
    }

    /**
     * Verifica se o usuário possui um papel específico (slug)
     */
    public function hasRole($roleSlug)
    {
        return $this->role && $this->role->slug === $roleSlug;
    }

    /**
     * Verifica se o usuário é Pastor
     */
    public function isPastor()
    {
        return $this->role && $this->role->slug === 'pastor';
    }

    /**
     * Verifica se o usuário tem acesso ao painel administrativo
     */
    public function hasAdminAccess()
    {
        return $this->isAdmin() || $this->isPastor();
    }

    /**
     * Campos utilizados para cálculo de completitude do perfil
     */
    public const PROFILE_FIELDS = [
        'first_name', 'last_name', 'cpf', 'date_of_birth', 'gender',
        'marital_status', 'email', 'phone', 'cellphone', 'address',
        'address_number', 'address_complement', 'neighborhood', 'city',
        'state', 'zip_code', 'title', 'is_ordained', 'ordination_date',
        'ministry_start_date', 'affiliated_church', 'baptist_convention',
        'theological_education', 'biography', 'is_baptized', 'baptism_date',
        'profession', 'education_level', 'workplace', 'emergency_contact_name',
        'emergency_contact_phone', 'emergency_contact_relationship',
    ];

    /**
     * Calcula a porcentagem de completitude do perfil
     */
    public function getProfileCompletionPercentage()
    {
        $filled = 0;
        foreach (self::PROFILE_FIELDS as $field) {
            $value = $this->$field;
            if ($value !== null && $value !== '') {
                $filled++;
            }
        }

        return round(($filled / count(self::PROFILE_FIELDS)) * 100);
    }

    /**
     * Relacionamento com registros de eventos
     */
    public function registrations()
    {
        return $this->hasMany(\Modules\Events\App\Models\EventRegistration::class);
    }

    /**
     * Relationship with Worship Academy Progress (v2 academy)
     */
    public function academyProgress()
    {
        return $this->hasMany(\Modules\Worship\App\Models\AcademyProgress::class, 'user_id');
    }

    /**
     * @deprecated Use academyProgress() for v2 academy. Legacy table worship_musician_progress.
     */
    public function worshipProgress()
    {
        return $this->hasMany(\Modules\Worship\App\Models\WorshipMusicianProgress::class, 'user_id');
    }

    /**
     * Retorna a foto ativa ou nulo
     */
    public function getActivePhoto()
    {
        return $this->profilePhotos()->where('is_active', true)->first();
    }

    /**
     * Get the photo URL.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
            return $this->photo;
        }

        return asset('storage/'.$this->photo);
    }

    /**
     * Retorna a URL do avatar do usuário
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->photo_url) {
            return $this->photo_url;
        }

        $activePhoto = $this->getActivePhoto();
        if ($activePhoto) {
            return asset('storage/'.$activePhoto->path);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Alias para exibição da foto de perfil (compatível com views que usam profile_photo_url).
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->avatar_url;
    }
}
