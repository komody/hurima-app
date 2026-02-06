<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品一覧 - Hurima</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/items/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/header.css') }}">
</head>

<body>
    @include('layouts.header')

    <main class="items-index">
        <div class="items-index-tabs">
            <a href="{{ route('items.index') }}"
                class="items-index-tab {{ request('tab') !== 'mylist' ? 'items-index-tab-active' : '' }}">
                おすすめ
            </a>
            <a href="{{ route('items.index', ['tab' => 'mylist']) }}"
                class="items-index-tab {{ request('tab') === 'mylist' ? 'items-index-tab-active' : '' }}">
                マイリスト
            </a>
        </div>

        <div class="items-index-content">
            @php
            $noImageUrl = asset('storage/layouts/no_image.png');
            @endphp
            <div class="items-index-product-grid">
                @forelse($products as $product)
                <a href="{{ route('items.show', ['item_id' => $product->id]) }}" class="items-index-product-card">
                    <div class="items-index-product-image">
                        <img src="{{ $product->image_url }}"
                            alt="{{ $product->name }}"
                            onerror="this.src='{{ $noImageUrl }}'">
                    </div>
                    <p class="items-index-product-name">{{ $product->name }}</p>
                </a>
                @empty
                <p>商品が見つかりませんでした。</p>
                @endforelse
            </div>
        </div>
    </main>
</body>

</html>