<!DOCTYPE html>
<html lang="ja">

<head>
    @include('partials.head')
    <style>
        body {
            overflow: hidden;
        }
    </style>
</head>

<body class="waiting-display" x-data="waitingDisplay()" x-init="init()">
    <header class="waiting-display__header">
        <div class="waiting-display__logo">
            平泉どうぶつ病院 待合室
        </div>
        <div class="waiting-display__updated">
            最終更新: <span x-text="lastUpdated">-</span>
        </div>
    </header>

    <div class="waiting-display__grid">
        <section class="waiting-display__panel">
            <h2 class="waiting-display__panel-title">ただいま診察中</h2>
            <div class="waiting-display__current-number" x-text="nowServing">-</div>
        </section>
        <section class="waiting-display__panel">
            <h2 class="waiting-display__panel-title">次の方</h2>
            <div class="waiting-display__secondary-number" x-text="nextServing">-</div>
        </section>
    </div>

    <section class="waiting-display__panel mx-12 mb-10">
        <h2 class="waiting-display__panel-title">お呼び出し予定</h2>
        <div class="waiting-display__queue">
            <template x-for="(item, index) in queue" :key="index">
                <div class="waiting-display__queue-item">
                    <span class="waiting-display__queue-number">#<span x-text="item.seq_no ?? item.id"></span></span>
                    <span class="waiting-display__queue-label" x-text="index === 0 ? '次に診察' : 'あと ' + (index + 1) + ' 番目'">
                    </span>
                </div>
            </template>
            <template x-if="queue.length === 0">
                <div class="waiting-display__queue-item text-base text-slate-300">
                    現在お待ちの方はいません。
                </div>
            </template>
        </div>
    </section>

    @vite(['resources/js/app.js'])

    <script>
        function waitingDisplay() {
            return {
                nowServing: '-',
                nextServing: '-',
                queue: [],
                lastUpdated: '-',
                async fetchStatus() {
                    try {
                        const response = await fetch('{{ route('status.json') }}', { cache: 'no-store' });
                        if (!response.ok) {
                            throw new Error('failed');
                        }
                        const data = await response.json();
                        this.nowServing = data.now_serving ? (data.now_serving.seq_no ?? data.now_serving.id) : '-';
                        this.nextServing = data.queue?.[0] ? (data.queue[0].seq_no ?? data.queue[0].id) : '-';
                        this.queue = data.queue ?? [];
                        this.lastUpdated = new Date().toLocaleTimeString();
                    } catch (e) {
                        this.lastUpdated = '更新に失敗しました';
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
