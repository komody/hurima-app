<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Requests\LoginRequest;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Notifications\VerifyEmail;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // 登録後のリダイレクト先を設定
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // 登録成功後のリダイレクト
        $this->app->singleton(\Laravel\Fortify\Contracts\RegisterResponse::class, function () {
            return new class implements \Laravel\Fortify\Contracts\RegisterResponse {
                public function toResponse($request)
                {
                    return redirect()->route('verification.notice');
                }
            };
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        $this->app->singleton(\Laravel\Fortify\Contracts\LoginResponse::class, function () {
            return new class implements \Laravel\Fortify\Contracts\LoginResponse {
                public function toResponse($request)
                {
                    $user = $request->user();

                    // メール認証未完了の場合（会員登録時のメール認証）
                    if (is_null($user->email_verified_at)) {
                        return redirect()->route('verification.notice');
                    }

                    // 初回ログイン時のメール認証未完了の場合
                    if (is_null($user->first_login_email_verified_at)) {
                        // 初回ログイン時のメール認証を送信（強制的に送信）
                        $user->notify(new VerifyEmail);
                        session()->put('first_login', true);
                        return redirect()->route('verification.notice');
                    }

                    // プロフィール未完了の場合
                    if (!$user->account || empty($user->account->postal_code) || empty($user->account->address)) {
                        return redirect()->route('mypage.profile.edit');
                    }

                    // 通常のログイン成功時
                    $intendedUrl = session()->get('url.intended');

                    // コメント投稿またはいいねを試みていた場合は商品詳細へ
                    if ($intendedUrl && preg_match('#/item/(\d+)(?:/(?:comment|like))?#', $intendedUrl, $matches)) {
                        return redirect()->route('items.show', ['item_id' => $matches[1]]);
                    }

                    return redirect()->route('items.index');
                }
            };
        });

        // ログイン失敗時の処理
        Fortify::authenticateUsing(function (Request $request) {
            // LoginRequestを使ってバリデーション
            $loginRequest = new LoginRequest();
            $validator = \Illuminate\Support\Facades\Validator::make(
                $request->all(),
                $loginRequest->rules(),
                $loginRequest->messages()
            );

            if ($validator->fails()) {
                // バリデーションエラーをセッションに保存してリダイレクト
                throw \Illuminate\Validation\ValidationException::withMessages($validator->errors()->toArray());
            }

            // バリデーション通過後、認証処理
            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
                return $user;
            }

            // ログイン失敗時のエラーメッセージをセッションに保存
            session()->flash('errors', collect(['ログイン情報が登録されていません']));
            return null;
        });
    }
}
