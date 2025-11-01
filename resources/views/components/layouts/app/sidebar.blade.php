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
            </div>

            <!-- 歩く猫のアニメーション -->
            <div class="walking-cat-container">
                <img src="{{ asset('images/nekowalk.gif') }}" alt="歩く猫" class="walking-cat-gif">
            </div>
        </header>

        <main class="clinic-main">
            {{ $slot }}
        </main>
    </div>

    @vite(['resources/js/app.js'])
</body>

</html>
