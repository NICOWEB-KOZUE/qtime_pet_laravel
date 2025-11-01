<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'kana',
        'pet_name',
        'pet_type',
        'pet_type_other',
        'phone',
        'email',
        'card_number',
        'password',
    ];

    /**
     * ペットの種類の選択肢を取得
     */
    public static function getPetTypes(): array
    {
        return [
            'dog' => '犬',
            'cat' => '猫',
            'rabbit' => 'うさぎ',
            'hamster' => 'ハムスター',
            'bird' => '鳥',
            'other' => 'その他',
        ];
    }

    /**
     * 患者が持つ受付チケット。
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
