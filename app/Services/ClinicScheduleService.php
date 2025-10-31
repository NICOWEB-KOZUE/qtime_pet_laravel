<?php

namespace App\Services;

use Carbon\Carbon;

class ClinicScheduleService
{
    /**
     * 今日が休診かどうかを返す（[bool, string]）。
     */
    public function isClosed(?Carbon $date = null, ?string $session = null): array
    {
        $date ??= Carbon::today();
        $session ??= $this->currentSession();

        $weekday = $date->englishDayOfWeek; // Monday, Tuesday, ...

        if ($this->closedFullWeekdays()->contains($weekday)) {
            return [true, '本日は休診日です（終日）'];
        }

        if ($session === 'PM') {
            if ($this->closedPmWeekdays()->contains($weekday)) {
                return [true, '本日の午後は休診です'];
            }

            if ($this->holidayPmDates()->contains($date->toDateString())) {
                return [true, '本日の午後は祝日のため休診です'];
            }
        }

        return [false, ''];
    }

    /**
     * 午前・午後の判定。
     */
    public function currentSession(): string
    {
        return Carbon::now()->hour < 12 ? 'AM' : 'PM';
    }

    /**
     * テンプレート用に休診情報をまとめて返す。
     */
    public function clinicContext(): array
    {
        $today = Carbon::today();
        $session = $this->currentSession();
        [$closed, $reason] = $this->isClosed($today, $session);

        return [
            'date' => $today->toDateString(),
            'session' => $session,
            'is_closed' => $closed,
            'closed_reason' => $reason,
        ];
    }

    /**
     * 終日休診の曜日（env から取得）。
     */
    protected function closedFullWeekdays()
    {
        return $this->parseWeekdayCsv(env('CLOSED_FULL_WEEKDAYS', 'Thu')) ?: collect(['Thursday']);
    }

    /**
     * 午後のみ休診の曜日（env から取得）。
     */
    protected function closedPmWeekdays()
    {
        return $this->parseWeekdayCsv(env('CLOSED_PM_WEEKDAYS', 'Sun')) ?: collect(['Sunday']);
    }

    /**
     * 午後休診にしたい特定日（YYYY-MM-DD）。
     */
    protected function holidayPmDates()
    {
        return collect(explode(',', env('HOLIDAY_PM_DATES', '')))
            ->map(fn($item) => trim($item))
            ->filter();
    }

    /**
     * env の曜日カンマ区切りを Carbon の英語表記に揃えて返す。
     *
     * 例: "Thu" -> "Thursday"
     */
    protected function parseWeekdayCsv(?string $csv)
    {
        return collect(explode(',', (string) $csv))
            ->map(fn($item) => trim($item))
            ->filter()
            ->map(function (string $token) {
                return match (strtolower($token)) {
                    'mon', 'monday' => 'Monday',
                    'tue', 'tues', 'tuesday' => 'Tuesday',
                    'wed', 'wednesday' => 'Wednesday',
                    'thu', 'thur', 'thurs', 'thursday' => 'Thursday',
                    'fri', 'friday' => 'Friday',
                    'sat', 'saturday' => 'Saturday',
                    'sun', 'sunday' => 'Sunday',
                    default => null,
                };
            })
            ->filter();
    }
}
