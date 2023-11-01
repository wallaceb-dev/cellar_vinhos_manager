<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function givePermissionTo(string $permission): void
    {
        $retrievedPermission = Permission::getPermission($permission);

        $this->permissions()->attach($retrievedPermission);

        Cache::forget('user::permissions::' . $this->id);
    }

    public function removePermissionTo(string $permission): void
    {
        $retrievedPermission = Permission::getPermission($permission);

        $this->permissions()->detach($retrievedPermission);

        Cache::forget('user::permissions::' . $this->id);
    }

    public function hasPermissionTo(string $permission): bool
    {
        $userPermissions = Cache::remember('user::permissions::' . $this->id, 86400, fn () => $this->permissions()->get());

        return $userPermissions->where('permission', $permission)->isNotEmpty();
    }
}
