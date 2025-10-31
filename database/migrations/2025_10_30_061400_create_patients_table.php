<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // 氏名
            $table->string('kana')->nullable();        // フリガナ（任意）
            $table->string('pet_name');                // ペット名
            $table->string('phone');                   // 電話番号
            $table->date('birth');                     // 生年月日
            $table->string('email')->nullable();       // メールアドレス（任意）
            $table->string('card_number')->unique();   // 診察券番号（ユニーク）
            $table->string('password');                // パスワード（生年月日下4桁）
            $table->timestamps();

            // インデックス
            $table->index('card_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
