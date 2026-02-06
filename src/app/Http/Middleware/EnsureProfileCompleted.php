<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureProfileCompleted
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
   * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
   */
  public function handle(Request $request, Closure $next)
  {
    if (Auth::check()) {
      $user = Auth::user();

      // postal_codeまたはaddressがnullの場合はプロフィール設定画面にリダイレクト
      if (is_null($user->postal_code) || is_null($user->address)) {
        // プロフィール設定画面へのアクセスは許可（無限ループを防ぐ）
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
