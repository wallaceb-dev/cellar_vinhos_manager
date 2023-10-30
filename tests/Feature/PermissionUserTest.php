<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermissionUserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_it_should_be_able_to_give_permission_to_an_user(): void
    {
        $user = User::factory()->create();

        $user->givePermissionTo('edit-products');

        $this->assertTrue($user->hasPermissionTo('edit-products'));
        $this->assertDatabaseHas('permissions', ['permission' => 'edit-products']);
    }
}
