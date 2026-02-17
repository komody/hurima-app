<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品購入 - Hurima</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/purchase/show.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/header.css') }}">
</head>

<body>
    @include('layouts.header', ['headerType' => 'login'])

    <main class="purchase-show">
        <div class="purchase-show-container">
            @if (session('message'))
            <div class="purchase-show-message">{{ session('message') }}</div>
            @endif
            @if (session('error'))
            <div class="purchase-show-message purchase-show-error">{{ session('error') }}</div>
            @endif
            <div class="purchase-show-main">
                <div class="purchase-show-detail-section">
                    <div class="purchase-show-product-section">
                        <div class="purchase-show-image-wrapper">
                            @php
                            $noImageUrl = asset('storage/layouts/no_image.png');
                            @endphp
                            <img src="{{ $item->image_url }}"
                                alt="{{ $item->name }}"
                                class="purchase-show-image"
                                onerror="this.src='{{ $noImageUrl }}'">
                        </div>
                        <div class="purchase-show-product-details">
                            <p class="purchase-show-product-name">{{ $item->name }}</p>
                            <p class="purchase-show-price">¥{{ number_format($item->price) }}</p>
                        </div>
                    </div>

                    <div class="purchase-show-payment-section">
                        <label for="payment_method" class="purchase-show-label">支払い方法</label>
                        <select id="payment_method" name="payment_method" class="purchase-show-select">
                            <option value="">選択してください</option>
                            <option value="コンビニ払い" {{ $paymentMethod === 'コンビニ払い' ? 'selected' : '' }}>コンビニ払い</option>
                            <option value="カード支払い" {{ $paymentMethod === 'カード支払い' ? 'selected' : '' }}>カード支払い</option>
                        </select>
                    </div>

                    <div class="purchase-show-delivery-section">
                        <div class="purchase-show-delivery-header">
                            <label class="purchase-show-label">配送先</label>
                            <a href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}" class="purchase-show-change-link">変更する</a>
                        </div>
                        <div class="purchase-show-delivery-address">
                            <p>〒 {{ $deliveryAddress['postal_code'] }}</p>
                            <p>{{ $deliveryAddress['address'] }}</p>
                            @if(!empty($deliveryAddress['building']))
                            <p>{{ $deliveryAddress['building'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="purchase-show-summary-section">
                    <div class="purchase-show-summary-box">
                        <div class="purchase-show-summary-row">
                            <span class="purchase-show-summary-label">商品代金</span>
                            <span class="purchase-show-summary-value">¥{{ number_format($item->price) }}</span>
                        </div>
                        <div class="purchase-show-summary-row">
                            <span class="purchase-show-summary-label">支払い方法</span>
                            <span id="payment_method_display" class="purchase-show-summary-value">{{ $paymentMethod ?: '選択してください' }}</span>
                        </div>
                        <button type="button" class="purchase-show-buy-btn" id="buy-btn">購入する</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('payment_method').addEventListener('change', function() {
            const display = document.getElementById('payment_method_display');
            display.textContent = this.value || '選択してください';
        });

        document.getElementById('buy-btn').addEventListener('click', async function() {
            const paymentMethod = document.getElementById('payment_method').value;

            if (!paymentMethod) {
                alert('支払い方法を選択してください。');
                return;
            }

            if (paymentMethod === 'コンビニ払い') {
                const btn = this;
                btn.disabled = true;
                btn.textContent = '処理中...';

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("purchase.complete-convenience", ["item_id" => $item->id]) }}';
                form.innerHTML = `@csrf`;
                document.body.appendChild(form);
                form.submit();
                return;
            }

            if (paymentMethod === 'カード支払い') {
                const btn = this;
                btn.disabled = true;
                btn.textContent = '処理中...';

                try {
                    const res = await fetch('{{ route("purchase.checkout", ["item_id" => $item->id]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });
                    const data = await res.json();

                    if (!res.ok) {
                        throw new Error(data.error || 'エラーが発生しました。');
                    }

                    if (data.url) {
                        window.location.href = data.url;
                    } else {
                        throw new Error('決済画面のURLを取得できませんでした。');
                    }
                } catch (err) {
                    alert(err.message || 'エラーが発生しました。');
                    btn.disabled = false;
                    btn.textContent = '購入する';
                }
            }
        });
    </script>
</body>

</html>