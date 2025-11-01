<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Ticket;
use Carbon\Carbon;

new #[Layout('components.layouts.admin')] class extends Component {
    public $pending = [];
    public $recent = [];

    public function mount(): void
    {
        $today = Carbon::today();

        $this->pending = Ticket::with('patient')->whereDate('visit_date', $today)->where('done', false)->orderBy('seq_no')->get();

        $this->recent = Ticket::with('patient')->whereDate('visit_date', $today)->where('done', true)->orderByDesc('seq_no')->limit(5)->get();
    }
};
?>

<div class="space-y-6">
    <div class="clinic-card">
        <div>
            <h1 class="clinic-card__title">管理者ダッシュボード</h1>
            <p class="clinic-card__subtitle">本日の受付状況と呼び出し操作を確認できます。</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="clinic-card">
            <h2 class="clinic-card__title text-xl">待ちリスト</h2>
            <p class="clinic-card__subtitle">未呼出の受付一覧です。</p>

            <div class="mt-4 divide-y divide-slate-200">
                @forelse ($pending as $ticket)
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3">
                            <div>
                                <p class="text-xl font-semibold text-slate-800">#{{ $ticket->seq_no ?? $ticket->id }}
                                </p>
                                <p class="text-base text-slate-500">
                                    {{ $ticket->patient->name ?? $ticket->name }}
                                    @if ($ticket->patient?->pet_type)
                                        （{{ \App\Models\Patient::getPetTypes()[$ticket->patient->pet_type] ?? $ticket->patient->pet_type }}）
                                    @endif
                                    ・{{ $ticket->visit_type ?? '診察内容未選択' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($ticket->notified)
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    通知済み
                                </span>
                            @endif
                            @if ($ticket->patient && $ticket->patient->email)
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-700"
                                    title="{{ $ticket->patient->email }}">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                                        <path
                                            d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                                    </svg>
                                    メール登録
                                </span>
                            @endif
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
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="text-xl font-semibold text-slate-800">#{{ $ticket->seq_no ?? $ticket->id }}</p>
                            <p class="text-base text-slate-500">
                                {{ $ticket->patient->name ?? $ticket->name }}
                                @if ($ticket->patient?->pet_type)
                                    （{{ \App\Models\Patient::getPetTypes()[$ticket->patient->pet_type] ?? $ticket->patient->pet_type }}）
                                @endif
                                ・ {{ $ticket->visit_type ?? '診察内容未選択' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($ticket->notified)
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    通知済み
                                </span>
                            @endif
                            @if ($ticket->patient && $ticket->patient->email)
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-700"
                                    title="{{ $ticket->patient->email }}">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                                        <path
                                            d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                                    </svg>
                                    メール登録
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="py-3 text-sm text-slate-500">呼び出し履歴はまだありません。</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('admin.manual') }}" class="mt-8 space-y-2">
                @csrf
                <label class="clinic-label" for="manual-name">手動受付追加</label>
                <input id="manual-name" type="text" name="name" class="clinic-input" placeholder="患者名（紙受付など）"
                    required>
                <button class="clinic-button--ghost w-full">受付に追加</button>
            </form>
        </section>
    </div>
</div>
