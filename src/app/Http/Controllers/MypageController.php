<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MypageController extends Controller
{
    /**
     * プロフィール画面を表示
     * - /mypage?page=buy: 購入した商品一覧
     * - /mypage?page=sell: 出品した商品一覧
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $page = $request->query('page') === 'buy' ? 'buy' : 'sell';

        $soldItems = $user->soldItems()->orderByDesc('created_at')->get();
        $purchasedItems = $user->purchasedItems()->orderByDesc('created_at')->get();

        return view('mypage.index', [
            'user' => $user,
            'soldItems' => $soldItems,
            'purchasedItems' => $purchasedItems,
            'page' => $page,
        ]);
    }

    /**
     * プロフィール編集画面を表示
     */
    public function editProfile()
    {
        return view('mypage.profile.edit');
    }

    public function updateProfile(ProfileRequest $request)
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインが必要です');
        }
        
        $validated = $request->validated();
        
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
