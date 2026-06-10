
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_keluar', function (Blueprint $table) {

            $table->unsignedBigInteger('penanggung_jawab_id')
                  ->nullable()
                  ->after('perihal');

        });

        Schema::table('surat_masuk', function (Blueprint $table) {

            $table->unsignedBigInteger('penanggung_jawab_id')
                  ->nullable()
                  ->after('perihal');

        });
    }

    public function down(): void
    {
        Schema::table('surat_keluar', function (Blueprint $table) {

            $table->dropColumn('penanggung_jawab_id');

        });

        Schema::table('surat_masuk', function (Blueprint $table) {

            $table->dropColumn('penanggung_jawab_id');

        });
    }
};

