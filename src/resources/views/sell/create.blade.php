<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品出品 - Hurima</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sell/create.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/header.css') }}">
</head>

<body>
    @include('layouts.header')

    <main class="sell-create">
        <h2 class="sell-create-title">商品の出品</h2>

        <form class="sell-create-form" method="POST" action="{{ route('sell.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- 商品画像 --}}
            <section class="sell-create-section">
                <div class="sell-create-image-title">商品画像</div>
                <div class="sell-create-image-area">
                    <label class="sell-create-image-label">
                        <input type="file" name="image" accept=".jpeg,.jpg,.png" class="sell-create-image-input">
                        <span class="sell-create-image-button">画像を選択する</span>
                    </label>
                </div>
                @error('image')
                <p class="sell-create-error">{{ $message }}</p>
                @enderror
            </section>

            {{-- 商品の詳細 --}}
            <section class="sell-create-section">
                <h3 class="sell-create-section-title">商品の詳細</h3>

                <div class="sell-create-field">
                    <label class="sell-create-label">カテゴリー</label>
                    <div class="sell-create-categories">
                        @foreach($categories as $category)
                        <label class="sell-create-category-tag">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                            <span class="sell-create-category-text">{{ $category->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('category_ids')
                    <p class="sell-create-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sell-create-field">
                    <label class="sell-create-label" for="condition_id">商品の状態</label>
                    <select name="condition_id" id="condition_id" class="sell-create-select">
                        <option value="">選択してください</option>
                        @foreach($conditions as $condition)
                        <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>
                            {{ $condition->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('condition_id')
                    <p class="sell-create-error">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            {{-- 商品名と説明 --}}
            <section class="sell-create-section">
                <h3 class="sell-create-section-title">商品名と説明</h3>

                <div class="sell-create-field">
                    <label class="sell-create-label" for="name">商品名</label>
                    <input type="text" name="name" id="name" class="sell-create-input"
                        value="{{ old('name') }}">
                    @error('name')
                    <p class="sell-create-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sell-create-field">
                    <label class="sell-create-label" for="brand_name">ブランド名</label>
                    <input type="text" name="brand_name" id="brand_name" class="sell-create-input"
                        value="{{ old('brand_name') }}">
                    @error('brand_name')
                    <p class="sell-create-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sell-create-field">
                    <label class="sell-create-label" for="description">商品の説明</label>
                    <textarea name="description" id="description" class="sell-create-textarea" rows="5">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="sell-create-error">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            {{-- 販売価格 --}}
            <section class="sell-create-section">
                <h3 class="sell-create-section-title">販売価格</h3>
                <div class="sell-create-field">
                    <label class="sell-create-label" for="price">価格</label>
                    <div class="sell-create-price-wrapper">
                        <input type="number" name="price" id="price" class="sell-create-input sell-create-price-input"
                            value="{{ old('price') }}" placeholder="￥" min="0" step="1">
                    </div>
                    @error('price')
                    <p class="sell-create-error">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            <button type="submit" class="sell-create-submit-btn">出品する</button>
        </form>
    </main>
</body>

</html>
