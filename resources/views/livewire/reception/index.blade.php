<?php

use Livewire\Volt\Component;
use App\Services\ClinicScheduleService;

new class extends Component {
    public array $clinicToday = [];

    public function mount(ClinicScheduleService $scheduleService): void
    {
        $this->clinicToday = $scheduleService->clinicContext();
    }
};

?>

<div class="max-w-5xl mx-auto px-3 py-0 space-y-5">
    <div class="space-y-2">
        <h1 class="text-4xl font-bold text-gray-900">本日の受付</h1>
        <p class="text-lg text-gray-600">当日の順番受付と診察状況を確認できます。</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-4 rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="space-y-2">
                <h2 class="text-2xl font-semibold text-gray-900">受付する</h2>
                <p class="text-base text-gray-600 leading-relaxed">
                    初めての方は「初回登録」、<br />
                    診察券をお持ちの方は「再診ログイン」へお進みください。
                </p>
            </div>

            <div class="grid gap-3">
                <a href="{{ route('register') }}"
                    class="flex items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-center text-lg font-semibold text-white shadow-sm transition hover:bg-blue-700">
                    📝 初回登録（はじめての方）
                </a>
                <a href="{{ route('patient.login') }}"
                    class="flex items-center justify-center rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-center text-lg font-semibold text-blue-700 transition hover:bg-blue-100">
                    🔑 再診ログイン（診察券あり）
                </a>
            </div>

            <div class="rounded-xl bg-blue-50 p-4 text-base text-blue-900">
                <h3 class="text-base font-semibold text-blue-700">診療時間</h3>
                <div class="mt-3 space-y-2">
                    <div>
                        <span class="font-semibold text-gray-800">午前診療</span> 9:00〜12:00<br>
                        <span class="text-sm text-gray-500">WEB受付 7:00〜11:00</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-800">午後診療</span> 15:00〜19:00<br>
                        <span class="text-sm text-gray-500">WEB受付 13:00〜18:00</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-5 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm" id="status-card">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">本日の診察状況</h2>
                <span
                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $clinicToday['is_closed'] ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                    {{ $clinicToday['session'] === 'AM' ? '午前診療' : '午後診療' }}
                </span>
            </div>

            @if ($clinicToday['is_closed'])
                <div class="rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
                    {{ $clinicToday['closed_reason'] ?: '現在は受付時間外です。' }}
                </div>
            @endif

            <div class="grid gap-4 md:grid-cols-2">
                <div id="nowSection" class="bg-blue-600 text-white rounded-xl p-5 space-y-2">
                    <div class="text-sm uppercase tracking-wide opacity-80">ただいま診察中</div>
                    <div class="text-4xl font-bold" id="now">-</div>
                    <div class="text-xs opacity-80">最新の診察番号</div>
                </div>

                <div id="nextSection" class="bg-blue-50 text-blue-700 rounded-xl p-5 space-y-2">
                    <div class="text-base uppercase tracking-wide opacity-80">次の方</div>
                    <div class="text-5xl font-bold" id="next">-</div>
                    <div class="text-sm opacity-80">待合中の先頭番号</div>
                </div>
            </div>

            <div>
                <div class="flex items-center gap-2 text-base font-semibold text-gray-800">
                    待ち状況
                    <span
                        class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-sm font-medium text-gray-600"
                        id="queueCount">- 人待ち</span>
                </div>
                <ul class="mt-3 space-y-1 text-base text-gray-700" id="queueList"></ul>
            </div>

            <div class="flex items-center justify-between">
                <button id="refreshBtn"
                    class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-base font-medium text-blue-700 transition hover:bg-blue-100">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.18A7.5 7.5 0 0020.03 9.354M17.016 4.35L20.197 1.17m0 0v4.992m0-4.993h-4.992" />
                    </svg>
                    最新情報に更新
                </button>
                <span class="text-xs text-gray-500" id="timestamp">最終更新 -</span>
            </div>
        </div>
    </div>
</div>

<script>
    async function loadStatus() {
        const nowElem = document.getElementById('now');
        const nextElem = document.getElementById('next');
        const queueList = document.getElementById('queueList');
        const queueCount = document.getElementById('queueCount');
        const ts = document.getElementById('timestamp');

        try {
            const response = await fetch('{{ route('status.json') }}', {
                cache: 'no-store'
            });
            if (!response.ok) {
                throw new Error('status API failed');
            }

            const data = await response.json();

            const nowServing = data.now_serving ? (data.now_serving.seq_no ?? data.now_serving.id) : '-';
            const nextServing = data.queue?.[0] ? (data.queue[0].seq_no ?? data.queue[0].id) : '-';

            nowElem.textContent = nowServing;
            nextElem.textContent = nextServing;

            queueList.innerHTML = '';
            (data.queue || []).slice(0, 6).forEach((item, index) => {
                const li = document.createElement('li');
                const number = item.seq_no ?? item.id;
                li.textContent = index === 0 ? `#${number}（次）` : `#${number}`;
                queueList.appendChild(li);
            });

            queueCount.textContent = `${data.queue?.length ?? 0} 人待ち`;
            ts.textContent = `最終更新 ${new Date().toLocaleTimeString()}`;
        } catch (error) {
            console.error(error);
            ts.textContent = `通信エラー ${new Date().toLocaleTimeString()}`;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('refreshBtn')?.addEventListener('click', loadStatus);
        setInterval(loadStatus, 5000);
        loadStatus();
    });
</script>
