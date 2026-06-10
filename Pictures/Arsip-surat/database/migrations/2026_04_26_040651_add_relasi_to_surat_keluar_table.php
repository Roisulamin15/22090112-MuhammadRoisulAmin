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
        Schema::table('surat_keluar', function (Blueprint $table) {
        $table->foreignId('pengirim_id')->nullable()->after('id');
        $table->foreignId('tujuan_id')->nullable()->after('pengirim_id');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_keluar', function (Blueprint $table) {
        $table->dropColumn(['pengirim_id', 'tujuan_id']);
    });
    }
};
