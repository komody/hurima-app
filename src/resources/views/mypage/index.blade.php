<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール - Hurima</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mypage/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/header.css') }}">
</head>

<body>
    @include('layouts.header', ['headerType' => 'login'])

    <main class="mypage">
        <div class="mypage-container">
            <div class="mypage-profile-section">
                <div class="mypage-profile-image-wrapper">
                    @if($user->account?->profile_image)
                    <img src="{{ asset('storage/' . $user->account->profile_image) }}" alt="プロフィール画像" class="mypage-profile-image">
                    @else
                    <div class="mypage-profile-image-placeholder"></div>
                    @endif
                </div>
                <div class="mypage-profile-info">
                    <p class="mypage-profile-name">{{ $user->name }}</p>
                    <a href="{{ route('mypage.profile.edit') }}" class="mypage-profile-edit-btn">プロフィールを編集</a>
                </div>
            </div>

            <div class="mypage-tabs">
                <a href="{{ route('mypage.index', ['page' => 'sell']) }}"
                    class="mypage-tab {{ $page === 'sell' ? 'mypage-tab-active' : '' }}">
                    出品した商品
                </a>
                <a href="{{ route('mypage.index', ['page' => 'buy']) }}"
                    class="mypage-tab {{ $page === 'buy' ? 'mypage-tab-active' : '' }}">
                    購入した商品
                </a>
            </div>

            <div class="mypage-content">
                @php
                $items = $page === 'buy' ? $purchasedItems : $soldItems;
                $noImageUrl = asset('storage/layouts/no_image.png');
                @endphp
                <div class="mypage-product-grid">
                    @forelse($items as $item)
                    <a href="{{ route('items.show', ['item_id' => $item->id]) }}" class="mypage-product-card">
                        <div class="mypage-product-image">
                            <img src="{{ $item->image_url }}"
                                alt="{{ $item->name }}"
                                onerror="this.src='{{ $noImageUrl }}'">
                        </div>
                        <p class="mypage-product-name">{{ $item->name }}</p>
                    </a>
                    @empty
                    <p class="mypage-empty-message">商品が見つかりませんでした。</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</body>

</html>
