<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private function validData(): array
    {
        return [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
    }

    public function test_name_is_required(): void
    {
        $data = $this->validData();
        $data['name'] = '';

        $response = $this->post(route('register'), $data);
        $response->assertSessionHasErrors('name');
    }

    public function test_email_is_required(): void
    {
        $data = $this->validData();
        $data['email'] = '';

        $response = $this->post(route('register'), $data);
        $response->assertSessionHasErrors('email');
    }

    public function test_password_is_required(): void
    {
        $data = $this->validData();
        $data['password'] = '';
        $data['password_confirmation'] = '';

        $response = $this->post(route('register'), $data);
        $response->assertSessionHasErrors('password');
    }

    public function test_password_must_be_at_least_8_characters(): void
    {
        $data = $this->validData();
        $data['password'] = '1234567';
        $data['password_confirmation'] = '1234567';

        $response = $this->post(route('register'), $data);
        $response->assertSessionHasErrors('password');
    }

    public function test_password_confirmation_must_match(): void
    {
        $data = $this->validData();
        $data['password_confirmation'] = 'different123';

        $response = $this->post(route('register'), $data);
        $response->assertSessionHasErrors('password');
    }

    public function test_user_can_register_with_valid_data(): void
    {
        $data = $this->validData();

        $response = $this->post(route('register'), $data);

        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect(route('verification.notice'));
    }
}
