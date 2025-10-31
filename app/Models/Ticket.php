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

    public const VISIT_TYPE_REVISIT = '再診';
    public const VISIT_TYPE_VACCINE = 'ワクチン';
    public const VISIT_TYPE_NAIL = '爪切り';
    public const VISIT_TYPE_OTHER = 'その他';

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
