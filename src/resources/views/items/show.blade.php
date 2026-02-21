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
                    <img src="{{ $item->image_url }}"
                        alt="{{ $item->name }}"
                        class="items-show-image"
                        onerror="this.src='{{ $noImageUrl }}'">
                    @if($item->sold_out)
                    <div class="items-show-sold-overlay">
                        <span class="items-show-sold-text">SOLD</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="items-show-details-section">
                <div class="items-show-header">
                    <p class="items-show-product-name">{{ $item->name }}</p>
                    @if($item->brand_name)
                    <p class="items-show-brand-name">{{ $item->brand_name }}</p>
                    @endif
                    <p class="items-show-price">¥<span class="items-show-price-value">{{ number_format($item->price) }}</span> (税込)</p>

                    <div class="items-show-stats">
                        <div class="items-show-stat">
                            @auth
                            <form method="POST" action="{{ route('items.like.toggle', ['item_id' => $item->id]) }}" class="items-show-like-form">
                                @csrf
                                <button type="submit" class="items-show-like-btn">
                                    <img src="{{ asset('storage/layouts/' . ($isLiked ? 'liked_icon.svg' : 'likes_icon.svg')) }}"
                                        alt="いいね"
                                        class="items-show-stat-icon">
                                </button>
                            </form>
                            @else
                            <a href="{{ route('login') }}?intended={{ urlencode(url()->current()) }}" class="items-show-like-link">
                                <img src="{{ asset('storage/layouts/likes_icon.svg') }}"
                                    alt="いいね"
                                    class="items-show-stat-icon">
                            </a>
                            @endauth
                            <span class="items-show-stat-count">{{ $likesCount }}</span>
                        </div>
                        <div class="items-show-stat">
                            <img src="{{ asset('storage/layouts/comments_icon.svg') }}"
                                alt="コメント"
                                class="items-show-stat-icon">
                            <span class="items-show-stat-count">{{ $item->comments_count }}</span>
                        </div>
                    </div>

                    <a href="{{ route('purchase.show', ['item_id' => $item->id]) }}" class="items-show-purchase-btn">
                        購入手続きへ
                    </a>
                </div>

                <div class="items-show-description-section">
                    <h3 class="items-show-section-title">商品説明</h3>
                    <div class="items-show-description-content">
                        @if($item->condition)
                        <p>商品の状態: {{ $item->condition->name }}</p>
                        @endif
                        <p>{{ $item->description }}</p>
                        <p>購入後、即発送いたします。</p>
                    </div>
                </div>

                <div class="items-show-info-section">
                    <h3 class="items-show-section-title">商品の情報</h3>
                    <div class="items-show-info-content">
                        @if($item->categories->count() > 0)
                        <div class="items-show-info-item">
                            <span class="items-show-info-label">カテゴリー</span>
                            <div class="items-show-categories">
                                @foreach($item->categories as $category)
                                <span class="items-show-category-tag">{{ $category->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($item->condition)
                        <div class="items-show-info-item">
                            <span class="items-show-info-label">商品の状態</span>
                            <span class="items-show-info-value">{{ $item->condition->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="items-show-comments-section">
                    <h3 class="items-show-section-title">コメント({{ $item->comments_count }})</h3>

                    <div class="items-show-comments-list">
                        @forelse($item->comments as $comment)
                        <div class="items-show-comment">
                            <div class="items-show-comment-header">
                                <div class="items-show-comment-avatar"></div>
                                <span class="items-show-comment-username">{{ $comment->user?->name ?? '退会ユーザー' }}</span>
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
                        <form class="items-show-comment-form" method="POST" action="{{ route('items.comment.store', ['item_id' => $item->id]) }}">
                            @csrf
                            @error('content')
                            <p class="items-show-comment-error">{{ $message }}</p>
                            @enderror
                            <textarea name="content"
                                class="items-show-comment-textarea"
                                placeholder="コメントを入力してください">{{ old('content') }}</textarea>
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