<!DOCTYPE html>
<html lang="ja">

<head>
    @include('partials.head')
    <style>
        :root {
            --blue-900: #1e3a8a;
            --blue-800: #1e40af;
            --blue-600: #2563eb;
            --blue-500: #3b82f6;
            --blue-300: #93c5fd;
            --blue-50: #eff6ff;
            --ink: #0f172a;
            --muted: #64748b;
            --panel: #ffffff;
        }

        html,
        body {
            height: 100%
        }

        body {
            margin: 0;
            padding: 0 0 100px 0;
            background: #fff;
            font-family: 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', Meiryo, system-ui, sans-serif;
            color: var(--ink);
            overflow: hidden;
        }

        /* ===== Hero（現在番号） ===== */
        .hero {
            height: 40vh;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            background: linear-gradient(135deg, var(--blue-500) 0%, var(--blue-300) 100%);
            padding-bottom: 2rem;
        }

        .hero__wrap {
            text-align: center
        }

        .hero__badge {
            display: inline-block;
            min-width: min(64vw, 880px);
            background: var(--blue-800);
            color: #fff;
            padding: clamp(12px, 2.2vh, 20px) clamp(24px, 4vw, 64px);
            border-radius: 16px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, .15);
        }

        .hero__number {
            font-weight: 800;
            line-height: 1;
            font-size: 140px;
            letter-spacing: .02em;
            min-width: 200px;
            display: inline-block;
        }

        .hero__note {
            margin-top: clamp(8px, 1vh, 16px);
            color: #fff;
            font-size: clamp(18px, 3.2vw, 28px);
            font-weight: 600;
        }

        /* ===== ボード（待ち番号） ===== */
        .board {
            height: 60vh;
            background: var(--blue-50);
            padding: clamp(16px, 2.4vh, 32px);
        }

        .board__inner {
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-head {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin: 0 0 14px;
        }

        .section-head__tag {
            background: #fff;
            border: 2px solid var(--blue-800);
            border-radius: 999px;
            padding: .35rem .9rem;
            font-weight: 700;
            font-size: clamp(14px, 2.4vw, 18px);
            color: var(--blue-800);
        }

        .section-head__sub {
            color: #ef4444;
            font-weight: 600;
            font-size: 20px;
        }

        .grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(6, 1fr);
        }

        .ticket {
            background: #fff;
            border: 3px solid var(--blue-500);
            border-radius: 12px;
            text-align: center;
            padding: 18px;
            font-weight: 800;
            color: var(--blue-800);
            font-size: 40px;
            box-shadow: 0 2px 0 rgba(59, 130, 246, .25);
            min-height: 90px;
            min-width: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ticket--soon {
            border-color: #10b981;
            color: #065f46;
            box-shadow: 0 2px 0 rgba(16, 185, 129, .25);
            background: #ecfdf5;
        }

        .empty {
            text-align: center;
            color: var(--muted);
            font-size: clamp(18px, 3.2vw, 24px);
            margin-top: 2rem;
        }

        /* ===== お知らせバー ===== */
        #tickerbar {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            height: 100px;
            background: var(--blue-900);
            border-top: 4px solid var(--blue-500);
            overflow: hidden;
            z-index: 1000;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, .08);
        }

        .ticker {
            position: relative;
            width: 100%;
            height: 100%
        }

        .ticker__track {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            white-space: nowrap;
            animation: ticker 28s linear infinite;
        }

        .ticker__item {
            display: inline-block;
            padding: 0 4rem;
            font-weight: 800;
            letter-spacing: .06em;
            font-size: 36px;
            color: #fff;
        }

        @keyframes ticker {
            0% {
                transform: translateX(100%)
            }

            100% {
                transform: translateX(-100%)
            }
        }

        /* ===== 歩く猫のアニメーション ===== */
        .walking-cats-container {
            position: fixed;
            top: calc(40vh - 70px);
            left: 0;
            width: 100%;
            height: 80px;
            pointer-events: none;
            z-index: 999;
            overflow: visible;
        }

        .walking-cat {
            position: absolute;
            bottom: 0;
            height: 70px;
            width: auto;
        }

        .walking-cat--1 {
            animation: cat-walk-1 35s linear infinite;
        }

        .walking-cat--2 {
            animation: cat-walk-2 45s linear infinite;
            animation-delay: 8s;
        }

        @keyframes cat-walk-1 {
            0% {
                right: -150px;
            }

            100% {
                right: calc(100% + 150px);
            }
        }

        @keyframes cat-walk-2 {
            0% {
                right: -150px;
            }

            100% {
                right: calc(100% + 150px);
            }
        }
    </style>
</head>

<body x-data="waitingDisplay()" x-init="init()">

    <!-- 現在の診察番号 -->
    <section class="hero" aria-label="現在の診察番号">
        <div class="hero__wrap">
            <div
                style="color: #fff; font-size: 2.5rem; font-weight: bold; margin-bottom: 1.5rem; letter-spacing: 0.1em;">
                診察中
            </div>
            <div class="hero__badge" role="status" aria-live="polite">
                <div class="hero__number" x-text="nowServing">100</div>
            </div>
            <div class="hero__note">番の方まで診察しています</div>
        </div>
    </section>

    <!-- 待ち番号 -->
    <section class="board" aria-label="待ち番号">
        <div class="board__inner">

            <!-- まもなく呼出（次の8件） -->
            <div class="section-head" style="margin-top:.25rem">
                <div class="section-head__tag">まもなくお呼び出し</div>
                <div class="section-head__sub">席を外される場合はスタッフへお声がけください</div>
            </div>
            <div class="grid" style="margin-bottom:14px">
                <template x-for="(n, i) in soonQueue" :key="'soon-' + i">
                    <div class="ticket ticket--soon" x-text="n"></div>
                </template>
                <template x-if="soonQueue.length === 0">
                    <div class="empty">まもなくお呼び出しの方はいません</div>
                </template>
            </div>

            <!-- 未呼出（残り） -->
            <div class="section-head" style="margin-top:10px">
                <div class="section-head__tag">未呼出</div>
                <div class="section-head__sub">お呼びするまでしばらくお待ちください</div>
            </div>
            <div class="grid">
                <template x-for="(n, i) in laterQueue" :key="'later-' + i">
                    <div class="ticket" x-text="n"></div>
                </template>
                <template x-if="laterQueue.length === 0">
                    <div class="empty">現在お待ちの方はいません</div>
                </template>
            </div>

        </div>
    </section>

    <!-- 歩く猫のアニメーション -->
    <div class="walking-cats-container">
        <img src="{{ asset('images/nekowalk.gif') }}" alt="歩く猫1" class="walking-cat walking-cat--1">
        <img src="{{ asset('images/nekowalk.gif') }}" alt="歩く猫2" class="walking-cat walking-cat--2">
    </div>

    <!-- お知らせ -->
    <div id="tickerbar" aria-label="本日のお知らせ">
        <div class="ticker">
            <div class="ticker__track">
                <span class="ticker__item">お知らせ：9月24日（水）は休診となります</span>
                <span class="ticker__item">フィラリア予防のご相談は受付まで</span>
            </div>
        </div>
    </div>

    @vite(['resources/js/app.js'])

    <script>
        function waitingDisplay() {
            return {
                nowServing: '100',
                waitingQueue: [],
                soonQueue: [],
                laterQueue: [],
                async fetchStatus() {
                    try {
                        const res = await fetch('{{ route('status.json') }}', {
                            cache: 'no-store'
                        });
                        if (!res.ok) throw new Error('failed');
                        const data = await res.json();

                        // 現在番号（診察済みの最新）
                        this.nowServing = data.now_serving ? String(data.now_serving.seq_no ?? data.now_serving.id) :
                            '-';

                        // 待ち行列を notified フラグで分類
                        const queue = data.queue ?? [];

                        // まもなくお呼び出し = メール通知済み（notified = true）
                        const notifiedTickets = queue.filter(i => i.notified === true);
                        this.soonQueue = notifiedTickets.map(i => String(i.seq_no ?? i.id));

                        // 未呼出 = メール未通知（notified = false）
                        const unnotifiedTickets = queue.filter(i => i.notified === false);
                        this.laterQueue = unnotifiedTickets.map(i => String(i.seq_no ?? i.id));

                        // 既存互換用（全体）
                        this.waitingQueue = queue.map(i => String(i.seq_no ?? i.id));
                    } catch (e) {
                        console.error('Status fetch error:', e);
                    }
                },
                init() {
                    this.fetchStatus();
                    setInterval(() => this.fetchStatus(), 5000);
                }
            }
        }
    </script>
</body>

</html>
