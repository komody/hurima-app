const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    // 共通スタイル
    .sass('resources/css/app.scss', 'public/css')
    // 商品関連
    .sass('resources/css/items/index.scss', 'public/css/items')
    .sass('resources/css/items/show.scss', 'public/css/items')
    .sass('resources/css/products/index.scss', 'public/css/products')
    // 認証関連
    .sass('resources/css/auth/login.scss', 'public/css/auth')
    .sass('resources/css/auth/register.scss', 'public/css/auth')
    .sass('resources/css/auth/verify-email.scss', 'public/css/auth')
    // 購入関連
    .sass('resources/css/purchase/show.scss', 'public/css/purchase')
    .sass('resources/css/purchase/address/edit.scss', 'public/css/purchase/address')
    // 出品関連
    .sass('resources/css/sell/create.scss', 'public/css/sell')
    // マイページ関連
    .sass('resources/css/mypage/index.scss', 'public/css/mypage')
    .sass('resources/css/mypage/profile/edit.scss', 'public/css/mypage/profile')
    // レイアウト関連
    .sass('resources/css/layouts/header.scss', 'public/css/layouts')
    // その他
    .sass('resources/css/welcome.scss', 'public/css');
