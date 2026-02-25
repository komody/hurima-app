<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_email_is_sent_after_registration(): void
    {
        Notification::fake();

        $data = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->post(route('register'), $data);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_verification_notice_page_has_link_to_mail_site(): void
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'unverified1@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => null,
            'first_login_email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get(route('verification.notice'));

        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');
        $response->assertSee('http://localhost:8025');
    }

    public function test_verification_redirects_to_profile_edit_when_completed(): void
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'unverified2@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => null,
            'first_login_email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('mypage.profile.edit'));
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
