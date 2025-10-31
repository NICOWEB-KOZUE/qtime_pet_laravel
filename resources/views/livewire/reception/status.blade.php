<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $nowServing = '-';
    public string $nextServing = '-';
    public array $queue = [];
    public string $lastUpdated = '-';

    public function mount(): void
    {
        $this->loadStatus();
    }

    public function loadStatus(): void
    {
        try {
            $response = Http::timeout(5)->get(route('status.json'));
            if ($response->ok()) {
                $data = $response->json();

                $this->nowServing = $data['now_serving']['seq_no'] ?? $data['now_serving']['id'] ?? '-';
                $this->nextServing = $data['queue'][0]['seq_no'] ?? $data['queue'][0]['id'] ?? '-';
                $this->queue = $data['queue'];
                $this->lastUpdated = now()->format('H:i:s');
            }
        } catch (\Throwable $e) {
            $this->addError('status', '診察状況の取得に失敗しました。時間を置いて再度お試しください。');
        }
    }

    public function refresh(): void
    {
        $this->loadStatus();
    }
};
?>

<div class="space-y-6">
    <div class="clinic-ticket-card">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-slate-800">診察状況</h1>
            <button class="clinic-button--ghost" wire:click="refresh">最新の情報に更新</button>
        </div>
        <p class="text-sm text-slate-500">最終更新: {{ $lastUpdated }}</p>

        @error('status')
            <div class="clinic-alert clinic-alert--danger mt-4">{{ $message }}</div>
        @enderror

        <div class="clinic-status mt-6">
            <div class="clinic-status__panel">
                <p class="clinic-status__heading">ただいま診察中</p>
                <p class="clinic-status__now">{{ $nowServing }}</p>
            </div>

            <div class="clinic-status__panel">
                <p class="clinic-status__heading">次の方</p>
                <p class="clinic-status__next">{{ $nextServing }}</p>
            </div>
        </div>

        <div class="clinic-status__panel mt-6">
            <p class="clinic-status__heading">待合中の方</p>
            <div class="clinic-status__queue">
                @forelse ($queue as $index => $item)
                    <div class="clinic-status__queue-item">
                        <span class="clinic-status__queue-number">
                            #{{ $item['seq_no'] ?? $item['id'] }}
                        </span>
                        <span class="text-xs text-slate-400">
                            {{ $index === 0 ? '次の診察' : '待ち順 ' . ($index + 1) }}
                        </span>
                    </div>
                @empty
                    <div class="px-4 py-3 text-sm text-slate-500">
                        現在お待ちの方はいません。
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
