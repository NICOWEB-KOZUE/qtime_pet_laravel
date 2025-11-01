<?php

namespace App\Services;

use App\Mail\TwoAheadNotification;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * 「あと2人」の患者に通知メールを送信
     * 管理者が「次を呼び出す」ボタンを押した後に実行される
     */
    public function notifyIfTwoAhead(): void
    {
        // 通知機能が無効の場合はスキップ
        if (! config('app.notify_enabled', false)) {
            Log::info('[NOTIFY] Notification is disabled');
            return;
        }

        $today = Carbon::today();

        // 本日の未完了チケットを取得
        $queue = Ticket::with('patient')
            ->whereDate('visit_date', $today)
            ->where('done', false)
            ->orderBy('seq_no')
            ->get();

        // 先頭から3番目（index=2）が「あと2人」の患者
        if ($queue->count() < 3) {
            Log::info('[NOTIFY SKIP] Less than 3 tickets in queue');
            return;
        }

        $target = $queue->get(2); // 0=次, 1=あと1人, 2=あと2人

        // すでに通知済みの場合はスキップ
        if ($target->notified) {
            Log::info("[NOTIFY SKIP] Already notified ticket_id={$target->id}");
            return;
        }

        // メールアドレスがある場合はメール送信
        if ($target->patient && $target->patient->email) {
            try {
                Mail::to($target->patient->email)->send(new TwoAheadNotification($target));
                Log::info("[EMAIL SENT] to={$target->patient->email} ticket_id={$target->id}");
            } catch (\Exception $e) {
                Log::error("[EMAIL ERROR] to={$target->patient->email} error={$e->getMessage()}");
            }
        } else {
            Log::info("[EMAIL SKIP] No email for ticket_id={$target->id}, but marking as notified");
        }

        // メールアドレスの有無に関わらず、通知済みフラグを立てる
        $target->update(['notified' => true]);
    }
}
