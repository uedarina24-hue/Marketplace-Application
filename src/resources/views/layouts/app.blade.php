<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>

    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
</head>

<body>

<div class="app">

    <!-- ヘッダー -->
    <header class="header">
        <div class="header__inner container">

            <!-- ロゴ -->
            <div class="header__logo">
                <a href="{{ route('items.index') }}">
                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="COACHTECH"
                        class="header__logo-img"
                    >
                </a>
            </div>


            <!-- 検索 -->
            <div class="header__search">
                <form
                    action="{{ route('items.index') }}"
                    method="GET"
                    class="search-form"
                >
                    <input
                        type="search"
                        name="keyword"
                        class="search-form__input"
                        placeholder="なにをお探しですか？"
                        value="{{ request('keyword', session('keyword')) }}"
                    >
                </form>
            </div>


            <!-- ナビ -->
            <nav class="header__nav">
                <ul class="header__nav-list">
                    {{-- ログイン・ログアウト切り替え --}}
                    @auth
                        <li class="header__nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="header__nav-link">
                                    ログアウト
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="header__nav-item">
                            <a href="{{ route('login') }}" class="header__nav-link">
                                ログイン
                            </a>
                        </li>
                    @endauth

                    {{-- 常に表示 --}}
                    <li class="header__nav-item">
                        <a href="{{ route('mypage') }}" class="header__nav-link">
                            マイページ
                        </a>
                    </li>

                    <li class="header__nav-item">
                        <a href="{{ route('items.create') }}" class="header__nav-link header__nav-link--button">
                            出品
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>



    <!-- フラッシュメッセージ -->
    @if(session('success'))
        <div class="flash flash--success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="flash flash--error">
            {{ session('error') }}
        </div>
    @endif



    <!-- メイン -->
    <main class="main">
        @yield('content')
    </main>

</div>
@yield('js')
</body>
</html>