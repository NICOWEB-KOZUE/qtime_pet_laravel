<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    public const SESSION_AM = 'AM';
    public const SESSION_PM = 'PM';

    // 診察内容の定数
    public const VISIT_TYPE_FIRST = '初診';
    public const VISIT_TYPE_REVISIT = '再診';
    public const VISIT_TYPE_VACCINE = 'ワクチン・予防接種';
    public const VISIT_TYPE_CHECKUP = '健康診断';
    public const VISIT_TYPE_GROOMING = '爪切り・耳掃除など';
    public const VISIT_TYPE_SURGERY = '手術・処置';
    public const VISIT_TYPE_EMERGENCY = '急患';
    public const VISIT_TYPE_OTHER = 'その他';

    /**
     * 診察内容の選択肢を取得
     */
    public static function getVisitTypes(): array
    {
        return [
            self::VISIT_TYPE_FIRST => '初診（はじめての来院）',
            self::VISIT_TYPE_REVISIT => '再診（経過観察・継続治療）',
            self::VISIT_TYPE_VACCINE => 'ワクチン・予防接種',
            self::VISIT_TYPE_CHECKUP => '健康診断',
            self::VISIT_TYPE_GROOMING => '爪切り・耳掃除など',
            self::VISIT_TYPE_SURGERY => '手術・処置',
            self::VISIT_TYPE_EMERGENCY => '急患（緊急対応）',
            self::VISIT_TYPE_OTHER => 'その他',
        ];
    }

    protected $fillable = [
        'patient_id',
        'name',
        'visit_date',
        'session',
        'seq_no',
        'done',
        'notified',
        'visit_type',
        'visit_type_other',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'done' => 'bool',
        'notified' => 'bool',
    ];

    /**
     * チケットに紐づく患者。
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
