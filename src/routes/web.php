<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\MypageController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 商品一覧画面 (トップ画面)
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 会員登録画面
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');

// ログイン画面
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// 商品詳細画面
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

// 商品購入画面
Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');

// 送付先住所変更画面
Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');

// 商品出品画面
Route::get('/sell', [SellController::class, 'create'])->name('sell.create');

// プロフィール画面
Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');

// プロフィール編集画面
Route::get('/mypage/profile', [MypageController::class, 'editProfile'])->name('mypage.profile.edit');

// 商品一覧ページ（既存のルート）
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::put('/mypage/profile', [MypageController::class, 'updateProfile'])->middleware('auth')->name('mypage.profile.update');

// メール認証誘導画面
Route::get('/email/verify', function () {
  return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// メール認証処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
  $user = $request->user();

  // 会員登録時のメール認証の場合
  if (is_null($user->email_verified_at)) {
    $request->fulfill();
  }
  // 初回ログイン時のメール認証の場合
  elseif (is_null($user->first_login_email_verified_at) && session('first_login')) {
    $user->first_login_email_verified_at = now();
    $user->save();
  }

  // プロフィール未完了の場合はプロフィール設定画面へ
  if (is_null($user->postal_code) || is_null($user->address)) {
    return redirect()->route('mypage.profile.edit');
  }

  return redirect()->route('items.index');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メール再送
Route::post('/email/verification-notification', function (Request $request) {
  $request->user()->sendEmailVerificationNotification();
  return back()->with('message', '認証メールを送信しました');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');