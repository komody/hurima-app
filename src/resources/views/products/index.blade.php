<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品一覧 - Hurima</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- ヘッダー -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <h1 class="text-2xl font-bold text-gray-900">Hurima</h1>
                <nav class="flex space-x-4">
                    <a href="#" class="text-gray-600 hover:text-gray-900">商品一覧</a>
                    <a href="#" class="text-gray-600 hover:text-gray-900">マイページ</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- メインコンテンツ -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- ページタイトル -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">商品一覧</h2>
            <p class="mt-2 text-gray-600">お気に入りの商品を見つけましょう</p>
        </div>

        <!-- フィルター・検索バー -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" placeholder="商品名で検索..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option>すべてのカテゴリー</option>
                <option>カテゴリー1</option>
                <option>カテゴリー2</option>
            </select>
            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option>並び順</option>
                <option>価格が安い順</option>
                <option>価格が高い順</option>
                <option>新着順</option>
            </select>
        </div>

        <!-- 商品グリッド -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- 商品カード -->
            @forelse($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <!-- 商品画像 -->
                <div class="relative aspect-square bg-gray-200">
                    <img src="{{ $product->image_url }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover"
                         onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">
                    <div class="absolute top-2 right-2">
                        <button class="bg-white rounded-full p-2 shadow-md hover:bg-gray-50">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                    </div>
                    @if($product->sold_out)
                    <div class="absolute top-2 left-2">
                        <span class="bg-red-500 text-white px-2 py-1 rounded text-sm font-semibold">売り切れ</span>
                    </div>
                    @endif
                </div>
                
                <!-- 商品情報 -->
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                        {{ $product->name }}
                    </h3>
                    @if($product->brand_name)
                    <p class="text-sm text-gray-500 mb-2">{{ $product->brand_name }}</p>
                    @endif
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl font-bold text-gray-900">¥{{ number_format($product->price) }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500 space-x-4">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"></path>
                            </svg>
                            {{ $product->likes_count }}
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $product->comments_count }}
                        </span>
                    </div>
                    @if($product->condition)
                    <p class="text-xs text-gray-400 mt-2">状態: {{ $product->condition->name }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-1">配送先: {{ $product->delivery_address }}</p>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 text-lg">商品が見つかりませんでした。</p>
            </div>
            @endforelse
        </div>

        <!-- ページネーション -->
        <div class="mt-8 flex justify-center">
            <nav class="flex space-x-2">
                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50" disabled>
                    前へ
                </button>
                <button class="px-4 py-2 bg-blue-500 text-white rounded-lg">1</button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">2</button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">3</button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    次へ
                </button>
            </nav>
        </div>
    </main>

    <!-- フッター -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center text-gray-600">
                <p>&copy; 2024 Hurima. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
