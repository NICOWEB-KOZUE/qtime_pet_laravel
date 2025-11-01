<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-slate-200">

<head>
    @include('partials.head')
</head>

<body class="clinic-layout">
    <div class="clinic-wrapper">
        <header class="admin-header">
            <div class="admin-header__inner">
                <div class="admin-header__title">QTime PET 管理</div>
                <div class="admin-header__spacer"></div>
                <nav class="admin-nav">
                    <a href="{{ route('status') }}" class="admin-nav__link">
                        診察状況（患者向け）
                    </a>
                    <a href="{{ route('display') }}" class="admin-nav__link">
                        院内ディスプレイ
                    </a>
                    <span class="admin-nav__badge">✅ 通知済み</span>
                    <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="admin-nav__link admin-nav__link--logout">
                            ログアウト
                        </button>
                    </form>
                </nav>
            </div>
        </header>

        <main class="clinic-main">
            {{ $slot }}
        </main>
    </div>

    @vite(['resources/js/app.js'])
</body>

</html>
