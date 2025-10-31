<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $pin = '';
    public ?string $errorMessage = null;

    public function submit(): void
    {
        $expect = config('app.admin_pin', env('ADMIN_PIN'));

        if (! $expect) {
            $this->errorMessage = '管理者PINが設定されていません。';
            return;
        }

        if ($this->pin === $expect) {
            session(['admin' => true]);
            $this->errorMessage = null;
            redirect()->route('admin.dashboard');
        } else {
            $this->errorMessage = 'PINが違います。';
            $this->reset('pin');
        }
    }
};
?>

<div class="max-w-sm mx-auto clinic-card">
    <h1 class="clinic-card__title">管理者ログイン</h1>
    <p class="clinic-card__subtitle">受付スタッフ専用の画面です。</p>

    @if ($errorMessage)
        <div class="clinic-alert clinic-alert--danger">{{ $errorMessage }}</div>
    @endif

    <form wire:submit.prevent="submit" class="clinic-form">
        <div class="clinic-field">
            <label class="clinic-label" for="pin">PINコード</label>
            <input id="pin" type="password" class="clinic-input" wire:model.defer="pin" autofocus>
        </div>

        <div class="clinic-actions">
            <button type="submit" class="clinic-button--primary">ログイン</button>
            <a href="{{ route('home') }}" class="clinic-button--ghost">トップへ戻る</a>
        </div>
    </form>
</div>
