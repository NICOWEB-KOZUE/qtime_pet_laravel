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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained()->onDelete('set null'); // 患者ID（外部キー）
            $table->string('name');                     // 表示用名前
            $table->date('visit_date');                 // 来院日
            $table->enum('session', ['AM', 'PM'])->default('AM'); // 午前/午後
            $table->integer('seq_no')->index();         // 当日連番
            $table->boolean('done')->default(false);    // 診察済みフラグ
            $table->boolean('notified')->default(false); // 通知済みフラグ

            // 診察内容（新規追加項目）
            $table->enum('visit_type', ['再診', 'ワクチン', '爪切り', 'その他'])->nullable();
            $table->string('visit_type_other')->nullable(); // 「その他」の詳細

            $table->timestamps();

            // 複合インデックス（日付と連番で検索効率化）
            $table->index(['visit_date', 'seq_no']);
            $table->index(['visit_date', 'done']); // 未完了チケット検索用
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
