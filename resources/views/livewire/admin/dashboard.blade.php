<?php

use Livewire\Volt\Component;
use App\Models\Ticket;
use Carbon\Carbon;

new class extends Component {
    public $pending = [];
    public $recent = [];

    public function mount(): void
    {
        $today = Carbon::today();

        $this->pending = Ticket::with('patient')
            ->whereDate('visit_date', $today)
            ->where('done', false)
            ->orderBy('seq_no')
            ->get();

        $this->recent = Ticket::with('patient')
            ->whereDate('visit_date', $today)
            ->where('done', true)
            ->orderByDesc('seq_no')
            ->limit(5)
            ->get();
    }
};
?>

<div class="space-y-6">
    <div class="clinic-card">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="clinic-card__title">管理者ダッシュボード</h1>
                <p class="clinic-card__subtitle">本日の受付状況と呼び出し操作を確認できます。</p>
            </div>

            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button class="clinic-button--ghost">ログアウト</button>
            </form>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="clinic-card">
            <h2 class="clinic-card__title text-xl">待ちリスト</h2>
            <p class="clinic-card__subtitle">未呼出の受付一覧です。</p>

            <div class="mt-4 divide-y divide-slate-200">
                @forelse ($pending as $ticket)
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="text-lg font-semibold text-slate-800">#{{ $ticket->seq_no ?? $ticket->id }}</p>
                            <p class="text-sm text-slate-500">
                                {{ $ticket->patient->name ?? $ticket->name }}
                                ・{{ $ticket->visit_type ?? '診察内容未選択' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="py-3 text-sm text-slate-500">現在お待ちの患者はいません。</p>
                @endforelse
            </div>

            <div class="clinic-actions mt-6">
                <form method="POST" action="{{ route('admin.next') }}">
                    @csrf
                    <button type="submit" class="clinic-button--primary">次を呼び出す</button>
                </form>

                <form method="POST" action="{{ route('admin.undo') }}">
                    @csrf
                    <button type="submit" class="clinic-button--ghost">直前の呼出を戻す</button>
                </form>
            </div>
        </section>

        <section class="clinic-card">
            <h2 class="clinic-card__title text-xl">直近の呼び出し</h2>
            <p class="clinic-card__subtitle">最新5件の履歴です。</p>

            <div class="mt-4 divide-y divide-slate-200">
                @forelse ($recent as $ticket)
                    <div class="py-3">
                        <p class="text-lg font-semibold text-slate-800">#{{ $ticket->seq_no ?? $ticket->id }}</p>
                        <p class="text-sm text-slate-500">
                            {{ $ticket->patient->name ?? $ticket->name }} ・ {{ $ticket->visit_type ?? '診察内容未選択' }}
                        </p>
                    </div>
                @empty
                    <p class="py-3 text-sm text-slate-500">呼び出し履歴はまだありません。</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('admin.manual') }}" class="mt-8 space-y-2">
                @csrf
                <label class="clinic-label" for="manual-name">手動受付追加</label>
                <input id="manual-name" type="text" name="name" class="clinic-input" placeholder="患者名（紙受付など）" required>
                <button class="clinic-button--ghost w-full">受付に追加</button>
            </form>
        </section>
    </div>
</div>
