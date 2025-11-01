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
        'phone',
        'email',
        'card_number',
        'password',
    ];

    /**
     * 患者が持つ受付チケット。
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
