<?php

use Livewire\Volt\Component;
use App\Services\ClinicScheduleService;
use App\Services\TicketService;
use App\Models\Patient;
use App\Models\Ticket;

new class extends Component {
    public array $clinicToday = [];

    public string $card = '';
    public string $pwd = '';
    public string $visit_type = '';
    public string $visit_type_other = '';

    public array $visitTypes = [];

    public function mount(ClinicScheduleService $scheduleService): void
    {
        $this->clinicToday = $scheduleService->clinicContext();
        $this->visitTypes = Ticket::getVisitTypes();
    }

    public function rules(): array
    {
        return [
            'card' => ['required', 'string', 'max:255'],
            'pwd' => ['required', 'string', 'max:255'],
            'visit_type' => ['nullable', 'in:' . implode(',', array_keys($this->visitTypes))],
            'visit_type_other' => ['required_if:visit_type,' . Ticket::VISIT_TYPE_OTHER, 'nullable', 'string', 'max:255'],
        ];
    }

    public function submit(ClinicScheduleService $scheduleService, TicketService $ticketService)
    {
        $this->validate();

        [$isClosed, $reason] = $scheduleService->isClosed();
        if ($isClosed) {
            $this->addError('form', $reason ?: '現在は受付時間外です。');
            return;
        }

        $patient = Patient::where('card_number', $this->card)->where('password', $this->pwd)->first();

        if (!$patient) {
            $this->addError('form', '診察券番号またはパスワードが違います。');
            return;
        }

        // 既存チケットがあるかチェック
        $today = \Carbon\Carbon::today();
        $existingTicket = \App\Models\Ticket::where('patient_id', $patient->id)->whereDate('visit_date', $today)->where('done', false)->first();

        $ticket = $ticketService->findOrCreateTodayTicket($patient, [
            'visit_type' => $this->visit_type ?: null,
            'visit_type_other' => $this->visit_type_other ?: null,
        ]);

        // 既存チケットがあった場合はフラッシュメッセージをセット
        if ($existingTicket) {
            session()->flash('already_registered', true);
        }

        return redirect()->route('done', ['ticket' => $ticket->id]);
    }
};
?>

<div class="space-y-8">
    <div class="clinic-card">
        <div>
            <h1 class="clinic-card__title">再診ログイン</h1>
            <p class="clinic-card__subtitle">診察券番号とパスワード（生年月日の下4桁）で順番受付ができます。</p>
        </div>

        @if ($clinicToday['is_closed'])
            <div class="clinic-alert clinic-alert--danger">
                {{ $clinicToday['closed_reason'] ?: '現在は受付時間外です。' }}
            </div>
        @endif

        @error('form')
            <div class="clinic-login__alert">{{ $message }}</div>
        @enderror

        <form wire:submit.prevent="submit" class="clinic-form">
            <div class="clinic-form__grid">
                <div class="clinic-field">
                    <label class="clinic-label" for="card">診察券番号 <span class="text-red-500">*</span></label>
                    <input id="card" type="text" class="clinic-input" wire:model.defer="card"
                        placeholder="例）C012345">
                    @error('card')
                        <p class="clinic-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="clinic-field">
                    <label class="clinic-label" for="pwd">パスワード <span class="text-red-500">*</span></label>
                    <input id="pwd" type="password" class="clinic-input" wire:model.defer="pwd"
                        placeholder="電話番号の下4桁">
                    @error('pwd')
                        <p class="clinic-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="clinic-field">
                    <label class="clinic-label" for="visit_type">診察内容</label>
                    <select id="visit_type" class="clinic-select" wire:model.live="visit_type">
                        <option value="">選択してください</option>
                        @foreach ($visitTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="clinic-hint">該当する内容があれば選択してください（任意）。</p>
                    @error('visit_type')
                        <p class="clinic-error">{{ $message }}</p>
                    @enderror
                </div>

                @if ($visit_type === Ticket::VISIT_TYPE_OTHER)
                    <div class="clinic-field">
                        <label class="clinic-label" for="visit_type_other">診察内容の詳細 <span
                                class="text-red-500">*</span></label>
                        <input id="visit_type_other" type="text" class="clinic-input"
                            wire:model.defer="visit_type_other" placeholder="例）耳のかゆみ">
                        @error('visit_type_other')
                            <p class="clinic-error">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>

            <div class="clinic-actions">
                <button type="submit" class="clinic-button--primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>受付する</span>
                    <span wire:loading>確認中...</span>
                </button>

                <a class="clinic-button--ghost" href="{{ route('home') }}">戻る</a>
            </div>
        </form>

        <div class="clinic-login__note">
            パスワードは「電話番号の下4桁」です。忘れた方は受付までご連絡ください。
        </div>
    </div>
</div>
