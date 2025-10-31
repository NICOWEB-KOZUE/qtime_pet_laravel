<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TicketService
{
    /**
     * 今日の未完了チケットを探し、なければ新規作成する。
     */
    public function findOrCreateTodayTicket(Patient $patient, array $attributes = []): Ticket
    {
        $today = Carbon::today();
        $session = $this->currentSession();

        $existing = Ticket::where('patient_id', $patient->id)
            ->whereDate('visit_date', $today)
            ->where('done', false)
            ->first();

        if ($existing) {
            // 診察内容が渡された場合は更新（再受付で変更したいケースに備える）
            if (array_key_exists('visit_type', $attributes)) {
                $existing->visit_type = $attributes['visit_type'] ?: null;
                $existing->visit_type_other = $this->resolveOtherDetail($attributes);
                $existing->save();
            }

            return $existing;
        }

        return DB::transaction(function () use ($patient, $today, $session, $attributes) {
            $seqNo = $this->nextSeqNoForDay($today);

            return Ticket::create([
                'patient_id' => $patient->id,
                'name' => $patient->name,
                'visit_date' => $today,
                'session' => $session,
                'seq_no' => $seqNo,
                'visit_type' => $attributes['visit_type'] ?? null,
                'visit_type_other' => $this->resolveOtherDetail($attributes),
            ]);
        });
    }

    /**
     * 指定日の次の受付番号を取得する（排他制御付き）。
     */
    public function nextSeqNoForDay(Carbon|string $date): int
    {
        $date = Carbon::parse($date)->startOfDay();

        $last = Ticket::whereDate('visit_date', $date)
            ->lockForUpdate()
            ->max('seq_no');

        return ($last ?? 0) + 1;
    }

    /**
     * 午前・午後の判定。
     */
    public function currentSession(): string
    {
        $hour = Carbon::now()->hour;

        return $hour < 12 ? Ticket::SESSION_AM : Ticket::SESSION_PM;
    }

    /**
     * 「その他」の詳細欄の値を整形。
     */
    private function resolveOtherDetail(array $attributes): ?string
    {
        $visitType = $attributes['visit_type'] ?? null;

        if ($visitType === Ticket::VISIT_TYPE_OTHER) {
            return $attributes['visit_type_other'] ?? null;
        }

        return null;
    }
}
