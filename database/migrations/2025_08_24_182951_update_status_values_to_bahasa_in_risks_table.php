<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Rename kolom
        Schema::table('risks', function ($table) {
            // kolom actor
            $table->renameColumn('created_by', 'dibuat_oleh');
            $table->renameColumn('reviewed_by', 'ditinjau_oleh');
            $table->renameColumn('approved_by', 'disetujui_oleh');
            // timestamp
            $table->renameColumn('approved_at', 'disetujui_pada');
            // status (dari approval_status → status_persetujuan)
            $table->renameColumn('approval_status', 'status_persetujuan');
        });

        // 2) Drop semua kemungkinan constraint lama (nama bisa berbeda-beda)
        DB::statement("ALTER TABLE risks DROP CONSTRAINT IF EXISTS risks_status_check");
        DB::statement("ALTER TABLE risks DROP CONSTRAINT IF EXISTS risks_approval_status_check");
        DB::statement("ALTER TABLE risks DROP CONSTRAINT IF EXISTS risks_status_persetujuan_check");

        // 3) Mapping nilai lama → Indonesia + sanitasi nilai kosong
        DB::statement("UPDATE risks SET status_persetujuan = 'draf' WHERE status_persetujuan IS NULL OR status_persetujuan = '' OR status_persetujuan = 'draft'");
        DB::statement("UPDATE risks SET status_persetujuan = 'ditinjau' WHERE status_persetujuan = 'reviewed'");
        DB::statement("UPDATE risks SET status_persetujuan = 'disetujui' WHERE status_persetujuan = 'approved'");
        DB::statement("UPDATE risks SET status_persetujuan = 'ditolak' WHERE status_persetujuan = 'rejected'");

        // 4) Tambah constraint baru (bahasa Indonesia)
        DB::statement("ALTER TABLE risks ADD CONSTRAINT risks_status_persetujuan_check CHECK (status_persetujuan IN ('draf','ditinjau','disetujui','ditolak'))");
    }

    public function down(): void
    {
        // Balik urutan: drop constraint baru → mapping balik → rename kolom
        DB::statement("ALTER TABLE risks DROP CONSTRAINT IF EXISTS risks_status_persetujuan_check");

        // Mapping balik ke Inggris
        DB::statement("UPDATE risks SET status_persetujuan = 'draft'     WHERE status_persetujuan = 'draf'");
        DB::statement("UPDATE risks SET status_persetujuan = 'reviewed'  WHERE status_persetujuan = 'ditinjau'");
        DB::statement("UPDATE risks SET status_persetujuan = 'approved'  WHERE status_persetujuan = 'disetujui'");
        DB::statement("UPDATE risks SET status_persetujuan = 'rejected'  WHERE status_persetujuan = 'ditolak'");

        // Tambahkan kembali constraint versi Inggris (opsional)
        DB::statement("ALTER TABLE risks ADD CONSTRAINT risks_approval_status_check CHECK (status_persetujuan IN ('draft','reviewed','approved','rejected'))");

        // Rename kolom kembali
        Schema::table('risks', function ($table) {
            $table->renameColumn('dibuat_oleh', 'created_by');
            $table->renameColumn('ditinjau_oleh', 'reviewed_by');
            $table->renameColumn('disetujui_oleh', 'approved_by');
            $table->renameColumn('disetujui_pada', 'approved_at');
            $table->renameColumn('status_persetujuan', 'approval_status');
        });
    }
};
