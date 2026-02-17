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
            <a href="{{ route('items.index', request()->only('search')) }}"
                class="items-index-tab {{ request('tab') !== 'mylist' ? 'items-index-tab-active' : '' }}">
                おすすめ
            </a>
            <a href="{{ route('items.index', array_merge(request()->only('search'), ['tab' => 'mylist'])) }}"
                class="items-index-tab {{ request('tab') === 'mylist' ? 'items-index-tab-active' : '' }}">
                マイリスト
            </a>
        </div>

        <div class="items-index-content">
            @php
            $noImageUrl = asset('storage/layouts/no_image.png');
            @endphp
            <div class="items-index-product-grid">
                @forelse($items as $item)
                @if($item->sold_out)
                <div class="items-index-product-card items-index-product-card-sold">
                    <div class="items-index-product-image items-index-product-image--sold">
                        <img src="{{ $item->image_url }}"
                            alt="{{ $item->name }}"
                            onerror="this.src='{{ $noImageUrl }}'">
                        <div class="items-index-product-sold-overlay">
                            <span class="items-index-product-sold-text">SOLD</span>
                        </div>
                    </div>
                    <p class="items-index-product-name">{{ $item->name }}</p>
                </div>
                @else
                <a href="{{ route('items.show', ['item_id' => $item->id]) }}" class="items-index-product-card">
                    <div class="items-index-product-image">
                        <img src="{{ $item->image_url }}"
                            alt="{{ $item->name }}"
                            onerror="this.src='{{ $noImageUrl }}'">
                    </div>
                    <p class="items-index-product-name">{{ $item->name }}</p>
                </a>
                @endif
                @empty
                <p>商品が見つかりませんでした。</p>
                @endforelse
            </div>
        </div>
    </main>
</body>

</html>