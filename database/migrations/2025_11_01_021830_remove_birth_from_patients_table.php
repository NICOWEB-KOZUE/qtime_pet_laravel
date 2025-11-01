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
        // 既存のパスワードを電話番号の下4桁に更新
        $patients = DB::table('patients')->get();
        foreach ($patients as $patient) {
            $phone = preg_replace('/[^0-9]/', '', $patient->phone);
            $newPassword = substr($phone, -4);
            DB::table('patients')
                ->where('id', $patient->id)
                ->update(['password' => $newPassword]);
        }

        // birth カラムを削除
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('birth');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->date('birth')->nullable();
        });
    }
};
