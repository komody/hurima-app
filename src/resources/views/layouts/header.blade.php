@php
$headerType = $headerType ?? 'not-login'; // デフォルト値
@endphp

<header class="header">
  <div class="header-wrapper">
    <h1 class="header-title">
      <img src="{{ asset('storage/layouts/title.svg') }}" alt="#">
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