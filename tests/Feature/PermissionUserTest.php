<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PermissionUserTest extends TestCase
{
    public function test_it_should_be_able_to_give_permission_to_an_user(): void
    {
        $user = User::factory()->create();

        $user->givePermissionTo('edit-products');

        $this->assertTrue($user->hasPermissionTo('edit-products'));
        $this->assertDatabaseHas('permissions', ['permission' => 'edit-products']);
    }

    public function test_it_should_allow_access_based_on_permission()
    {
        Route::middleware('permission:edit-products')->get('foo', function () {
            return 'lorem';
        });

        $user = User::factory()->createOne();

        $this->actingAs($user)->get('foo')->assertForbidden();

        $user->givePermissionTo('edit-products');

        $this->actingAs($user)->get('foo')->assertSuccessful();
    }

    public function test_user_permissions_should_be_cached()
    {
        Cache::flush();

        Permission::query()->create([
            'permission' => 'permission-test',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $cachedPermissions = Cache::get('permissions');

        $cachedTest = $cachedPermissions->firstWhere('permission', 'permission-test');

        $this->assertNotNull($cachedTest);

        Permission::where('permission', 'permission-test')->delete();
    }
}
