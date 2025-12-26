<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Modules\User\Models\Profile;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'photo_url',
        'first_name',
        'last_name',
        'middle_name',
        'lang',
        'ignore_maintenance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'ignore_maintenance' => 'boolean',
        ];
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    /**
     * Get accesses
     *
     * @return array<string, mixed>
     */
    public function getAccesses(): array
    {
        $cacheKey = 'users:' . $this->email . ':accesses';

        return Cache::remember(
            $cacheKey,
            now()->addHour(),
            function () {
                $response = Http::get(config('inStudy.sso_url') . '/api/accesses', [
                    'email' => $this->email,
                    'secret' => config('inStudy.inStudy_secret'),
                ]);

                if (!$response->successful()) {
                    return [];
                }

                $data = $response->json();
                return $data['accesses'] ?? [];
            }
        );
    }
}
