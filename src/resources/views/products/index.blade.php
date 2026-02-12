<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品一覧 - Hurima</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/products/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/header.css') }}">
</head>

<body class="products-index">
    <!-- ヘッダー -->
    @include('layouts.header')

    <!-- メインコンテンツ -->
    <main class="products-index__main">
        <!-- ページタイトル -->
        <div class="products-index__title-section">
            <h2 class="products-index__title">商品一覧</h2>
            <p class="products-index__subtitle">お気に入りの商品を見つけましょう</p>
        </div>

        <!-- フィルター・検索バー -->
        <div class="products-index__filter-section">
            <div class="products-index__search-wrapper">
                <input type="text" placeholder="商品名で検索..."
                    class="products-index__search-input">
            </div>
            <select class="products-index__filter-select">
                <option>すべてのカテゴリー</option>
                <option>カテゴリー1</option>
                <option>カテゴリー2</option>
            </select>
            <select class="products-index__filter-select">
                <option>並び順</option>
                <option>価格が安い順</option>
                <option>価格が高い順</option>
                <option>新着順</option>
            </select>
        </div>

        <!-- 商品グリッド -->
        <div class="products-index__product-grid">
            <!-- 商品カード -->
            @forelse($items as $item)
            <div class="products-index__product-card">
                <!-- 商品画像 -->
                <div class="products-index__product-image-wrapper">
                    <img src="{{ $item->image_url }}"
                        alt="{{ $item->name }}"
                        class="products-index__product-image"
                        onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">
                    <div class="products-index__product-like-button">
                        <button class="products-index__like-btn">
                            <svg class="products-index__like-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                    </div>
                    @if($item->sold_out)
                    <div class="products-index__sold-out-badge">
                        <span class="products-index__sold-out-text">売り切れ</span>
                    </div>
                    @endif
                </div>

                <!-- 商品情報 -->
                <div class="products-index__product-info">
                    <h3 class="products-index__product-name">
                        {{ $item->name }}
                    </h3>
                    @if($item->brand_name)
                    <p class="products-index__product-brand">{{ $item->brand_name }}</p>
                    @endif
                    <div class="products-index__product-price-wrapper">
                        <span class="products-index__product-price">¥{{ number_format($item->price) }}</span>
                    </div>
                    <div class="products-index__product-stats">
                        <span class="products-index__product-stat">
                            <svg class="products-index__stat-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"></path>
                            </svg>
                            {{ $item->likes_count }}
                        </span>
                        <span class="products-index__product-stat">
                            <svg class="products-index__stat-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $item->comments_count }}
                        </span>
                    </div>
                    @if($item->condition)
                    <p class="products-index__product-condition">状態: {{ $item->condition->name }}</p>
                    @endif
                    <p class="products-index__product-delivery">配送先: {{ $item->delivery_address }}</p>
                </div>
            </div>
            @empty
            <div class="products-index__empty">
                <p class="products-index__empty-text">商品が見つかりませんでした。</p>
            </div>
            @endforelse
        </div>

        <!-- ページネーション -->
        <div class="products-index__pagination">
            <nav class="products-index__pagination-nav">
                <button class="products-index__pagination-btn products-index__pagination-btn--disabled" disabled>
                    前へ
                </button>
                <button class="products-index__pagination-btn products-index__pagination-btn--active">1</button>
                <button class="products-index__pagination-btn">2</button>
                <button class="products-index__pagination-btn">3</button>
                <button class="products-index__pagination-btn">
                    次へ
                </button>
            </nav>
        </div>
    </main>

    <!-- フッター -->
    <footer class="products-index__footer">
        <div class="products-index__footer-container">
            <div class="products-index__footer-content">
                <p>&copy; 2024 Hurima. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>

</html>