<?php

use Livewire\Volt\Component;
use App\Models\Ticket;

new class extends Component {
    public Ticket $ticket;

    public function mount(Ticket $ticket): void
    {
        $this->ticket = $ticket->loadMissing(['patient']);
    }
};
?>

<div class="space-y-8">
    <div class="clinic-ticket-card">
        <div class="space-y-4 text-center">
            <p class="clinic-ticket__status">受付完了</p>
            <p class="clinic-ticket__number">#{{ $ticket->seq_no ?? $ticket->id }}</p>
            <p class="text-lg font-semibold text-slate-800">
                {{ $ticket->patient->name ?? $ticket->name }} 様
            </p>
            <p class="text-sm text-slate-600">
                受付番号をお控えのうえ、待合室でお待ちください。
            </p>
        </div>

        <dl class="clinic-ticket__info">
            <div>
                <dt>来院日</dt>
                <dd>{{ $ticket->visit_date?->format('Y年m月d日') }}</dd>
            </div>
            <div>
                <dt>受付区分</dt>
                <dd>{{ $ticket->session === 'PM' ? '午後の部' : '午前の部' }}</dd>
            </div>
            <div>
                <dt>診察内容</dt>
                <dd>
                    {{ $ticket->visit_type ?? '（未選択）' }}
                    @if ($ticket->visit_type === \App\Models\Ticket::VISIT_TYPE_OTHER && $ticket->visit_type_other)
                        <span class="text-slate-500">（{{ $ticket->visit_type_other }}）</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt>メール通知</dt>
                <dd>
                    @if ($ticket->patient?->email)
                        {{ $ticket->patient->email }}（順番が近づいたらお知らせします）
                    @else
                        登録なし
                    @endif
                </dd>
            </div>
        </dl>

        <div class="clinic-ticket__actions">
            <a href="{{ route('home') }}" class="clinic-button--primary">トップに戻る</a>
            <a href="{{ route('status') ?? '#' }}" class="clinic-button--ghost">
                診察状況を見る
            </a>
        </div>
    </div>

    <div class="clinic-alert clinic-alert--info">
        呼び出しの際は受付番号でご案内します。表示されるまでお待ちください。
    </div>
</div>
