<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function validData(): array
    {
        return [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];
    }

    public function test_email_is_required(): void
    {
        $data = $this->validData();
        $data['email'] = '';

        $response = $this->post(route('login'), $data);
        $response->assertSessionHasErrors('email');
    }

    public function test_password_is_required(): void
    {
        $data = $this->validData();
        $data['password'] = '';

        $response = $this->post(route('login'), $data);
        $response->assertSessionHasErrors('password');
    }

    public function test_login_fails_with_incorrect_credentials(): void
    {
        $data = $this->validData();

        $response = $this->from(route('login'))->post(route('login'), $data);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'ログイン情報が登録されていません',
            $response->getSession()->get('errors')->get('email')[0] ?? ''
        );
    }

    public function test_login_succeeds_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'first_login_email_verified_at' => now(),
        ]);
        Account::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'postal_code' => '1000001',
            'address' => '東京都千代田区',
        ]);

        $response = $this->post(route('login'), $this->validData());

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('items.index'));
    }
}
