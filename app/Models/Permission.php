<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['permission'];

    public static function getPermission(string $permission): Permission
    {
        $retrievePermissionFromCacheOrCreated = self::getAllFromCache()->where('permission', $permission)->first();

        if (!$retrievePermissionFromCacheOrCreated) {
            $retrievePermissionFromCacheOrCreated = Permission::query()->create(['permission' => $permission]);
        }

        return $retrievePermissionFromCacheOrCreated;
    }

    public static function getAllFromCache(): Collection
    {
        $permissions = Cache::remember('permissions', 86400, fn () => self::all());

        return $permissions;
    }

    public static function existsOnCache(string $permission): bool
    {
        return self::getAllFromCache()->where('permission', $permission)->isNotEmpty();
    }
}
