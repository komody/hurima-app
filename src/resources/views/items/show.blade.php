<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品詳細 - Hurima</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/header.css') }}">
</head>

<body>
    @include('layouts.header')

    <main class="items-show">
        <div class="items-show-container">
            <div class="items-show-image-section">
                <div class="items-show-image-wrapper">
                    @php
                    $noImageUrl = asset('storage/layouts/no_image.png');
                    @endphp
                    <img src="{{ $product->image_url }}"
                        alt="{{ $product->name }}"
                        class="items-show-image"
                        onerror="this.src='{{ $noImageUrl }}'">
                </div>
            </div>

            <div class="items-show-details-section">
                <div class="items-show-header">
                    <h1 class="items-show-product-name">{{ $product->name }}</h1>
                    @if($product->brand_name)
                    <p class="items-show-brand-name">{{ $product->brand_name }}</p>
                    @endif
                    <p class="items-show-price">¥{{ number_format($product->price) }} (税込)</p>

                    <div class="items-show-stats">
                        <div class="items-show-stat">
                            <img src="{{ asset('storage/layouts/likes_icon.svg') }}"
                                alt="いいね"
                                class="items-show-stat-icon">
                            <span class="items-show-stat-count">{{ $product->likes_count }}</span>
                        </div>
                        <div class="items-show-stat">
                            <img src="{{ asset('storage/layouts/comments_icon.svg') }}"
                                alt="コメント"
                                class="items-show-stat-icon">
                            <span class="items-show-stat-count">{{ $product->comments_count }}</span>
                        </div>
                    </div>

                    <a href="{{ route('purchase.show', ['item_id' => $product->id]) }}" class="items-show-purchase-btn">
                        購入手続きへ
                    </a>
                </div>

                <div class="items-show-description-section">
                    <h2 class="items-show-section-title">商品説明</h2>
                    <div class="items-show-description-content">
                        @if($product->condition)
                        <p>商品の状態: {{ $product->condition->name }}</p>
                        @endif
                        <p>{{ $product->description }}</p>
                        <p>購入後、即発送いたします。</p>
                    </div>
                </div>

                <div class="items-show-info-section">
                    <h2 class="items-show-section-title">商品の情報</h2>
                    <div class="items-show-info-content">
                        @if($product->categories->count() > 0)
                        <div class="items-show-info-item">
                            <span class="items-show-info-label">カテゴリー</span>
                            <div class="items-show-categories">
                                @foreach($product->categories as $category)
                                <span class="items-show-category-tag">{{ $category->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($product->condition)
                        <div class="items-show-info-item">
                            <span class="items-show-info-label">商品の状態</span>
                            <span class="items-show-info-value">{{ $product->condition->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="items-show-comments-section">
                    <h2 class="items-show-section-title">コメント({{ $product->comments_count }})</h2>

                    <div class="items-show-comments-list">
                        @forelse($product->comments as $comment)
                        <div class="items-show-comment">
                            <div class="items-show-comment-header">
                                <div class="items-show-comment-avatar"></div>
                                <span class="items-show-comment-username">{{ $comment->user->name ?? 'admin' }}</span>
                            </div>
                            <div class="items-show-comment-content">
                                {{ $comment->content }}
                            </div>
                        </div>
                        @empty
                        <p class="items-show-no-comments">コメントはまだありません。</p>
                        @endforelse
                    </div>

                    <div class="items-show-comment-form-section">
                        <h3 class="items-show-comment-form-title">商品へのコメント</h3>
                        <form class="items-show-comment-form" method="POST" action="#">
                            @csrf
                            <textarea name="content"
                                class="items-show-comment-textarea"
                                placeholder="コメントを入力してください"></textarea>
                            <button type="submit" class="items-show-comment-submit-btn">
                                コメントを送信する
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>