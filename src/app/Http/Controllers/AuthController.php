<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * 会員登録画面を表示
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * ログイン画面を表示
     */
    public function showLoginForm(Request $request)
    {
        if ($request->has('intended')) {
            session()->put('url.intended', $request->intended);
        }
        return view('auth.login');
    }
}
