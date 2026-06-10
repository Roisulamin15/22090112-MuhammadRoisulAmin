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
        if (!Schema::hasColumn('surat_keluar', 'pengirim_id')) {
            $table->foreignId('pengirim_id')->nullable();
        }

        if (!Schema::hasColumn('surat_keluar', 'tujuan_id')) {
            $table->foreignId('tujuan_id')->nullable();
        }
    });

    Schema::table('surat_masuk', function (Blueprint $table) {
        if (!Schema::hasColumn('surat_masuk', 'pengirim_id')) {
            $table->foreignId('pengirim_id')->nullable();
        }

        if (!Schema::hasColumn('surat_masuk', 'penerima_id')) {
            $table->foreignId('penerima_id')->nullable();
        }
    });
}
};
