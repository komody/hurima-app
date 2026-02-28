<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureProfileCompleted
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // アカウントが存在しない、または postal_code/address が未入力の場合はプロフィール設定画面へ
            if (!$user->account || empty($user->account->postal_code) || empty($user->account->address)) {
                if ($request->routeIs('mypage.profile.*')) {
                    return $next($request);
                }

                return redirect()->route('mypage.profile.edit')
                    ->with('message', 'プロフィール情報を入力してください');
            }
        }

        return $next($request);
    }
}
