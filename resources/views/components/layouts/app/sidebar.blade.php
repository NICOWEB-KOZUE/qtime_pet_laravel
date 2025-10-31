<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-white">

<head>
    @include('partials.head')
</head>

<body class="clinic-layout">
    <div class="clinic-wrapper">
        <header class="clinic-header">
            <div class="clinic-header__inner">
                <a href="{{ route('home') }}" class="clinic-brand">
                    <x-app-logo />
                </a>

                <nav class="clinic-nav">
                    <a href="{{ route('home') }}"
                        class="clinic-nav__link {{ request()->routeIs('home') ? 'is-active' : '' }}">ホーム</a>
                    <a href="{{ route('register') }}"
                        class="clinic-nav__link {{ request()->routeIs('register') ? 'is-active' : '' }}">初回登録</a>
                    <a href="{{ route('patient.login') }}"
                        class="clinic-nav__link {{ request()->routeIs('patient.login') ? 'is-active' : '' }}">再診ログイン</a>
                    <a href="{{ route('display') }}"
                        class="clinic-nav__link {{ request()->routeIs('display') ? 'is-active' : '' }}">待合モニター</a>
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
