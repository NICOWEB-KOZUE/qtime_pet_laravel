<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 既存データを新しい値にマッピング
        DB::table('tickets')->where('visit_type', 'ワクチン')->update(['visit_type' => 'ワクチン・予防接種']);
        DB::table('tickets')->where('visit_type', '爪切り')->update(['visit_type' => '爪切り・耳掃除など']);
        
        // enum型を変更
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('visit_type', [
                '初診',
                '再診',
                'ワクチン・予防接種',
                '健康診断',
                '爪切り・耳掃除など',
                '手術・処置',
                '急患',
                'その他'
            ])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 元のenum値に戻す
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('visit_type', ['再診', 'ワクチン', '爪切り', 'その他'])->nullable()->change();
        });
        
        // データを元に戻す
        DB::table('tickets')->where('visit_type', 'ワクチン・予防接種')->update(['visit_type' => 'ワクチン']);
        DB::table('tickets')->where('visit_type', '爪切り・耳掃除など')->update(['visit_type' => '爪切り']);
    }
};
