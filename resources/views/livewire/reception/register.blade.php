<?php

use Livewire\Volt\Component;
use App\Services\ClinicScheduleService;
use App\Services\TicketService;
use App\Models\Patient;
use App\Models\Ticket;
use Carbon\Carbon;

new class extends Component {
    public array $clinicToday = [];

    public string $name = '';
    public string $kana = '';
    public string $pet_name = '';
    public string $pet_type = '';
    public string $pet_type_other = '';
    public string $phone = '';
    public string $email = '';
    public string $visit_type = '';
    public string $visit_type_other = '';

    public array $petTypes = [];
    public array $visitTypes = [];

    public function mount(ClinicScheduleService $scheduleService): void
    {
        $this->clinicToday = $scheduleService->clinicContext();
        $this->petTypes = Patient::getPetTypes();
        $this->visitTypes = Ticket::getVisitTypes();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'kana' => ['nullable', 'string', 'max:255'],
            'pet_name' => ['required', 'string', 'max:255'],
            'pet_type' => ['nullable', 'in:' . implode(',', array_keys($this->petTypes))],
            'pet_type_other' => ['required_if:pet_type,other', 'nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'visit_type' => ['nullable', 'in:' . implode(',', array_keys($this->visitTypes))],
            'visit_type_other' => ['required_if:visit_type,' . Ticket::VISIT_TYPE_OTHER, 'nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'pet_type_other.required_if' => '「その他」を選択した場合は詳細を入力してください。',
            'visit_type_other.required_if' => '「その他」を選択した場合は詳細を入力してください。',
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

        $patient = Patient::create([
            'name' => $this->name,
            'kana' => $this->kana,
            'pet_name' => $this->pet_name,
            'pet_type' => $this->pet_type ?: null,
            'pet_type_other' => $this->pet_type_other ?: null,
            'phone' => $this->phone,
            'email' => $this->email ?: null,
            'card_number' => $this->generateCardNumber(),
            'password' => $this->passwordFromPhone($this->phone),
        ]);

        $ticket = $ticketService->findOrCreateTodayTicket($patient, [
            'visit_type' => $this->visit_type ?: null,
            'visit_type_other' => $this->visit_type_other ?: null,
        ]);

        return redirect()->route('done', ['ticket' => $ticket->id]);
    }

    private function generateCardNumber(): string
    {
        do {
            $number = 'C' . str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Patient::where('card_number', $number)->exists());

        return $number;
    }

    private function passwordFromPhone(string $phone): string
    {
        // 電話番号から数字のみを抽出して下4桁を取得
        $digits = preg_replace('/[^0-9]/', '', $phone);
        return substr($digits, -4);
    }
};
?>

<div class="space-y-8">
    <div class="clinic-card">
        <div>
            <h1 class="clinic-card__title">初回登録</h1>
            <p class="clinic-card__subtitle">はじめてご利用の方はこちらから順番受付を行ってください。</p>
        </div>

        @if ($clinicToday['is_closed'])
            <div class="clinic-alert clinic-alert--danger">
                {{ $clinicToday['closed_reason'] ?: '現在は受付時間外です。' }}
            </div>
        @endif

        @error('form')
            <div class="clinic-alert clinic-alert--danger">{{ $message }}</div>
        @enderror

        <form wire:submit.prevent="submit" class="clinic-form">
            <div class="clinic-form__grid">
                <div class="clinic-field">
                    <label class="clinic-label" for="name">お名前 <span class="text-red-500">*</span></label>
                    <input id="name" type="text" class="clinic-input" wire:model.defer="name"
                        placeholder="例）山田 太郎">
                    @error('name')
                        <p class="clinic-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="clinic-field">
                    <label class="clinic-label" for="kana">フリガナ</label>
                    <input id="kana" type="text" class="clinic-input" wire:model.defer="kana"
                        placeholder="例）ヤマダ タロウ">
                    @error('kana')
                        <p class="clinic-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="clinic-field">
                    <label class="clinic-label" for="pet_name">ペットのお名前 <span class="text-red-500">*</span></label>
                    <input id="pet_name" type="text" class="clinic-input" wire:model.defer="pet_name"
                        placeholder="例）ポチ">
                    @error('pet_name')
                        <p class="clinic-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="clinic-field">
                    <label class="clinic-label" for="pet_type">ペットの種類</label>
                    <select id="pet_type" class="clinic-select" wire:model.live="pet_type">
                        <option value="">選択してください</option>
                        @foreach ($petTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="clinic-hint">該当する種類があれば選択してください（任意）。</p>
                    @error('pet_type')
                        <p class="clinic-error">{{ $message }}</p>
                    @enderror
                </div>

                @if ($pet_type === 'other')
                    <div class="clinic-field">
                        <label class="clinic-label" for="pet_type_other">ペットの種類の詳細 <span
                                class="text-red-500">*</span></label>
                        <input id="pet_type_other" type="text" class="clinic-input" wire:model.defer="pet_type_other"
                            placeholder="例）フェレット">
                        @error('pet_type_other')
                            <p class="clinic-error">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div class="clinic-field">
                    <label class="clinic-label" for="phone">電話番号 <span class="text-red-500">*</span></label>
                    <input id="phone" type="tel" class="clinic-input" wire:model.defer="phone"
                        placeholder="090-1234-5678">
                    <p class="clinic-hint">パスワードとして電話番号の下4桁を利用します。</p>
                    @error('phone')
                        <p class="clinic-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="clinic-field">
                    <label class="clinic-label" for="email">メールアドレス</label>
                    <input id="email" type="email" class="clinic-input" wire:model.defer="email"
                        placeholder="example@example.com">
                    <p class="clinic-hint">順番が近づいたらお知らせします（任意）。</p>
                    @error('email')
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
                    <span wire:loading.remove>登録して受付する</span>
                    <span wire:loading>送信中...</span>
                </button>

                <a class="clinic-button--ghost" href="{{ route('home') }}">戻る</a>
            </div>
        </form>
    </div>
</div>
