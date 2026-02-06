<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール - Hurima</title>
</head>
<body>
    <h1>プロフィール画面</h1>
    @if(request('page') === 'buy')
        <p>購入した商品一覧</p>
    @elseif(request('page') === 'sell')
        <p>出品した商品一覧</p>
    @else
        <p>プロフィール情報</p>
    @endif
    <!-- ここにプロフィールのコンテンツを追加 -->
</body>
</html>
