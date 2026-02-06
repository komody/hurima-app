@php
// ログイン状態に応じて自動的にheaderTypeを設定
  if (!isset($headerType)) {
    if (auth()->check()) {
    $headerType = 'login';
    } else {
    $headerType = 'not-login';
    }
  }
@endphp

<header class="header">
  <div class="header-wrapper">
    <h1 class="header-title">
      <a href="{{ route('items.index') }}">
        <img src="{{ asset('storage/layouts/title.svg') }}" alt="#">
      </a>
    </h1>
    @if($headerType === 'login-page')
    {{-- ログイン画面 --}}
    @else
    {{-- ログイン以外の画面 --}}
    <div class="header-search">
      <input class="header-search-field" type="text" placeholder="なにをお探しですか？">
    </div>
    @endif
    <nav class="header-nav">
      @if($headerType === 'login')
      {{-- ログイン済み --}}
      <ul class="header-nav-list">
        <li class="header-nav-item">
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
          <a href="#" class="nav-logout-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a>
        </li>
        <li class="header-nav-item">
          <a href="{{ route('mypage.index') }}" class="nav-mypage-link">マイページ</a>
        </li>
        <li class="header-nav-item">
          <a href="{{ route('sell.create') }}" class="nav-sell-link">出品</a>
        </li>
      </ul>
      @elseif($headerType === 'not-login')
      {{-- 未ログイン --}}
      <ul class="header-nav-list">
        <li class="header-nav-item">
          <a href="{{ route('login') }}" class="nav-login-link">ログイン</a>
        </li>
        <li class="header-nav-item">
          <a href="{{ route('mypage.index') }}" class="nav-mypage-link">マイページ</a>
        </li>
        <li class="header-nav-item">
          <a href="{{ route('sell.create') }}" class="nav-sell-link">出品</a>
        </li>
      </ul>
      @else
      {{-- ログイン画面（ナビゲーションなし） --}}
      @endif
    </nav>
  </div>
</header>