<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('risks', function (Blueprint $table) {
            // Kolom peninjau
            $table->timestamp('ditinjau_pada')->nullable()->after('ditinjau_oleh');

            // Kolom penolak
            $table->unsignedBigInteger('ditolak_oleh')->nullable()->after('ditinjau_pada');
            $table->timestamp('ditolak_pada')->nullable()->after('ditolak_oleh');
            $table->text('alasan_penolakan')->nullable()->after('ditolak_pada');

            $table->foreign('ditolak_oleh')->references('id')->on('users')->nullOnDelete();
        });

        // Ubah constraint status_persetujuan supaya mendukung 'ditolak'
        DB::statement("ALTER TABLE risks DROP CONSTRAINT IF EXISTS risks_status_persetujuan_check");

        DB::statement("
            ALTER TABLE risks 
            ADD CONSTRAINT risks_status_persetujuan_check 
            CHECK (status_persetujuan IN ('draf','ditinjau','disetujui','ditolak'))
        ");
    }

    public function down(): void
    {
        // drop constraint dulu
        DB::statement("ALTER TABLE risks DROP CONSTRAINT IF EXISTS risks_status_persetujuan_check");

        // kembalikan constraint lama
        DB::statement("
            ALTER TABLE risks 
            ADD CONSTRAINT risks_status_persetujuan_check 
            CHECK (status_persetujuan IN ('draf','ditinjau','disetujui'))
        ");

        Schema::table('risks', function (Blueprint $table) {
            $table->dropForeign(['ditolak_oleh']);
            $table->dropColumn([
                'ditinjau_pada',
                'ditolak_oleh',
                'ditolak_pada',
                'alasan_penolakan',
            ]);
        });
    }
};
