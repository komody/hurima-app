<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_logout(): void
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'logout-test@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'first_login_email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect(route('items.index'));
    }
}
