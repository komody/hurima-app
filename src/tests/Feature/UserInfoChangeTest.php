<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 14. ユーザー情報変更
 */
class UserInfoChangeTest extends TestCase
{
    use RefreshDatabase;

    private function createVerifiedUser(array $accountAttributes = []): User
    {
        $user = User::create([
            'name' => '初期ユーザー名',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'first_login_email_verified_at' => now(),
        ]);
        Account::create(array_merge([
            'user_id' => $user->id,
            'name' => '初期ユーザー名',
            'postal_code' => '100-0001',
            'address' => '東京都千代田区千代田1-1',
            'building' => 'テストビル',
        ], $accountAttributes));
        return $user;
    }

    /**
     * 14.1 変更項目が初期値として過去設定が表示される
     */
    public function test_initial_values_are_displayed_on_profile_edit_page(): void
    {
        $user = $this->createVerifiedUser();

        $response = $this->actingAs($user)->get(route('mypage.profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('初期ユーザー名');
        $response->assertSee('100-0001');
        $response->assertSee('東京都千代田区千代田1-1');
        $response->assertSee('テストビル');
    }
}
