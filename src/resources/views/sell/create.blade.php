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

        <form class="sell-create-form" method="POST" action="{{ route('sell.store') }}" enctype="multipart/form-data" novalidate>
            @csrf

            {{-- 商品画像 --}}
            <section class="sell-create-section">
                <div class="sell-create-image-title">商品画像</div>
                <div class="sell-create-image-area">
                    <label class="sell-create-image-label">
                        <input type="file" name="image" class="sell-create-image-input">
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
                    <div class="custom-dropdown" id="condition-dropdown">
                        <input type="hidden" name="condition_id" id="condition_id" value="{{ old('condition_id') }}">
                        <button type="button" class="custom-dropdown-trigger" aria-expanded="false" aria-haspopup="listbox" aria-labelledby="condition-label">
                            <span class="custom-dropdown-value">選択してください</span>
                            <span class="custom-dropdown-arrow"></span>
                        </button>
                        <ul class="custom-dropdown-list" role="listbox" id="condition-list">
                            @foreach($conditions as $condition)
                            <li class="custom-dropdown-option" role="option" data-value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'data-selected' : '' }}>
                                {{ $condition->name }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
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
                        <input name="price" id="price" class="sell-create-input sell-create-price-input"
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdown = document.getElementById('condition-dropdown');
            const trigger = dropdown.querySelector('.custom-dropdown-trigger');
            const valueDisplay = dropdown.querySelector('.custom-dropdown-value');
            const hiddenInput = dropdown.querySelector('input[name="condition_id"]');
            const list = dropdown.querySelector('.custom-dropdown-list');
            const options = dropdown.querySelectorAll('.custom-dropdown-option');

            // 初期表示（old値がある場合）
            const selectedOption = dropdown.querySelector('.custom-dropdown-option[data-selected]');
            if (selectedOption) {
                valueDisplay.textContent = selectedOption.textContent.trim();
                valueDisplay.classList.add('has-value');
                selectedOption.classList.add('selected');
            }

            // 開閉
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('open');
            });

            // オプション選択
            options.forEach(function(option) {
                option.addEventListener('click', function(e) {
                    e.stopPropagation();
                    options.forEach(o => o.classList.remove('selected'));
                    option.classList.add('selected');
                    hiddenInput.value = option.dataset.value;
                    valueDisplay.textContent = option.textContent.trim();
                    valueDisplay.classList.add('has-value');
                    dropdown.classList.remove('open');
                });
            });

            // 外側クリックで閉じる
            document.addEventListener('click', function() {
                dropdown.classList.remove('open');
            });
        });
    </script>
</body>

</html>