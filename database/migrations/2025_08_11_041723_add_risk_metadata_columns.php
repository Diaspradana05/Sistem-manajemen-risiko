<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom baru (nullable dulu agar migrasi aman)
        Schema::table('risks', function (Blueprint $table) {
            if (!Schema::hasColumn('risks', 'company_id')) {
                $table->foreignId('company_id')
                    ->nullable()
                    ->constrained()
                    ->onDelete('cascade');
            }

            if (!Schema::hasColumn('risks', 'division_id')) {
                $table->foreignId('division_id')
                    ->nullable()
                    ->constrained()
                    ->onDelete('cascade');
            }

            if (!Schema::hasColumn('risks', 'year')) {
                $table->unsignedSmallInteger('year')->nullable();
            }
        });

        // Isi data lama dengan tahun sekarang
        DB::table('risks')->whereNull('year')->update([
            'year' => date('Y')
        ]);

        // Ubah jadi NOT NULL
        Schema::table('risks', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('risks', function (Blueprint $table) {
            if (Schema::hasColumn('risks', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }
            if (Schema::hasColumn('risks', 'division_id')) {
                $table->dropConstrainedForeignId('division_id');
            }
            if (Schema::hasColumn('risks', 'year')) {
                $table->dropColumn('year');
            }
        });
    }
};
