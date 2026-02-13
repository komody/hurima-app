<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\LikeController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\CommentController;
use App\Notifications\VerifyEmail;

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

// ログアウト
Route::post('/logout', function (Request $request) {
  Auth::logout();
  $request->session()->invalidate();
  $request->session()->regenerateToken();
  return redirect()->route('items.index');
})->middleware('auth')->name('logout');

// 商品詳細画面
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

// コメント投稿（認証必須）
Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])
  ->middleware('auth')
  ->name('items.comment.store');

// いいね機能（認証必須）
Route::post('/item/{item_id}/like', [LikeController::class, 'toggle'])
  ->middleware('auth')
  ->name('items.like.toggle');

// 商品一覧ページ（既存のルート）
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

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
  if (!$user->account || empty($user->account->postal_code) || empty($user->account->address)) {
    return redirect()->route('mypage.profile.edit');
  }

  return redirect()->route('items.index');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メール再送
Route::post('/email/verification-notification', function (Request $request) {
  $user = $request->user();

  // 初回ログイン時のメール認証の場合
  if (is_null($user->first_login_email_verified_at) && session('first_login')) {
    // 初回ログイン時のメール認証を送信（強制的に送信）
    $user->notify(new VerifyEmail);
  } else {
    // 会員登録時のメール認証を送信
    $user->sendEmailVerificationNotification();
  }

  return back()->with('message', '認証メールを送信しました');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// プロフィール設定画面以外の認証が必要なルートに適用
Route::middleware(['auth', 'profile.completed'])->group(function () {
  Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
  Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');
  Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
  Route::put('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
  Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
  // ... その他の認証が必要なルート
});

// プロフィール設定画面は認証のみ（profile.completedは適用しない）
Route::middleware('auth')->group(function () {
  Route::get('/mypage/profile', [MypageController::class, 'editProfile'])->name('mypage.profile.edit');
  Route::put('/mypage/profile', [MypageController::class, 'updateProfile'])->name('mypage.profile.update');
});
