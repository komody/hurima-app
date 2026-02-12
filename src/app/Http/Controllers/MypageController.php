<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MypageController extends Controller
{
    /**
     * プロフィール画面を表示
     */
    public function index(Request $request)
    {
        return view('mypage.index');
    }

    /**
     * プロフィール編集画面を表示
     */
    public function editProfile()
    {
        return view('mypage.profile.edit');
    }

    public function updateProfile(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインが必要です');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        // ユーザー名を更新
        $user->update(['name' => $validated['name']]);

        // Account がなければ作成、あれば更新
        $account = $user->account;

        if (!$account) {
            $account = $user->account()->create([
                'name' => $validated['name'],
                'postal_code' => $validated['postal_code'],
                'address' => $validated['address'],
                'building' => $validated['building'],
                'profile_image' => null,
            ]);
        }

        // プロフィール画像のアップロード処理
        if ($request->hasFile('profile_image')) {
            // 古い画像を削除
            if ($account->profile_image) {
                Storage::disk('public')->delete($account->profile_image);
            }

            // 新しい画像を保存
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $validated['profile_image'] = $path;
        }

        $account->fill([
            'name' => $validated['name'],
            'postal_code' => $validated['postal_code'],
            'address' => $validated['address'],
            'building' => $validated['building'],
            'profile_image' => $validated['profile_image'] ?? $account->profile_image,
        ]);
        $account->save();

        return redirect()->route('items.index')->with('success', 'プロフィールを更新しました');
    }
}
