{{ $patientName }} 様

平泉どうぶつ病院です。
本日の診察が **あと2名** でご案内となります。

- 受付番号：{{ $ticketNumber }}（{{ $session }}）
- 日付：{{ $visitDate }}
- 現在の進行状況：{{ $statusUrl }}

お早めに当院へお越しください。
来院が難しい場合は、お手数ですがお電話にてご連絡ください。

――
{{ config('app.clinic_name', '平泉どうぶつ病院') }}
TEL：{{ config('app.clinic_tel', '0191-00-0000') }}
このメールは送信専用です。返信には対応しておりません。
