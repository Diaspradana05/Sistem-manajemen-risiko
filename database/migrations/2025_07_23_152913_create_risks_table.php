<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan');
            $table->string('tujuan_kegiatan')->nullable();
            $table->string('area_lokasi')->nullable();
            $table->string('kode_risiko')->nullable();
            $table->string('risiko')->nullable();

            // Penyebab risiko
            $table->string('sebab_1')->nullable();
            $table->string('sebab_2')->nullable();
            $table->string('sebab_3')->nullable();
            $table->string('sebab_4')->nullable();
            $table->string('sebab_5')->nullable();

            $table->text('dampak')->nullable();
            $table->text('pernyataan_risiko')->nullable();
            $table->text('pengendalian_saat_ini')->nullable();

            // Analisa risiko
            $table->integer('analisa_dampak')->default(0);
            $table->integer('analisa_probabilitas')->default(0);
            $table->integer('analisa_conate')->default(0);
            $table->integer('skor')->default(0);
            $table->string('peringkat_risiko')->nullable();

            $table->boolean('perlu_penanganan')->default(false);

            // Opsi teknik pengendalian risiko
            $table->boolean('hindari_risiko')->default(false);
            $table->boolean('cegah_kerugian')->default(false);
            $table->boolean('reduksi_kerugian')->default(false);
            $table->boolean('segregasi')->default(false);
            $table->boolean('contractual_transfer')->default(false);

            $table->text('rencana_penanganan')->nullable();
            $table->text('pembiayaan_risiko')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risks');
    }
};
