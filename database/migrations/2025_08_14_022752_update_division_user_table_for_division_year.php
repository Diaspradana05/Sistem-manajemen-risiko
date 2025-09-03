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
        Schema::table('division_user', function (Blueprint $table) {
            // Hapus kolom year jika ada
            if (Schema::hasColumn('division_user', 'year')) {
                $table->dropColumn('year');
            }

            // Tambahkan kolom division_year_id
            $table->unsignedBigInteger('division_year_id')->nullable()->after('division_id');

            // Tambahkan foreign key ke tabel division_year
            $table->foreign('division_year_id')
                  ->references('id')
                  ->on('division_year')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('division_user', function (Blueprint $table) {
            // Hapus foreign key dan kolom division_year_id
            $table->dropForeign(['division_year_id']);
            $table->dropColumn('division_year_id');

            // Tambahkan kembali kolom year
            $table->integer('year')->nullable();
        });
    }
};
