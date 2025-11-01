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
        Schema::table('patients', function (Blueprint $table) {
            $table->enum('pet_type', ['dog', 'cat', 'rabbit', 'hamster', 'bird', 'other'])
                ->nullable()
                ->after('pet_name');
            $table->string('pet_type_other')->nullable()->after('pet_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['pet_type', 'pet_type_other']);
        });
    }
};
