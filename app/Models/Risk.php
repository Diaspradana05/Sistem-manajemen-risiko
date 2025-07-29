<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Risk extends Model
{
    protected $fillable = [
        'nama_kegiatan', 'tujuan_kegiatan', 'area_lokasi', 'kode_risiko', 'risiko',
        'sebab_1','sebab_2','sebab_3','sebab_4','sebab_5',
        'dampak','pernyataan_risiko','pengendalian_saat_ini',
        'analisa_dampak','analisa_probabilitas','analisa_conate','skor','peringkat_risiko',
        'perlu_penanganan','hindari_risiko','cegah_kerugian','reduksi_kerugian','segregasi',
        'contractual_transfer','rencana_penanganan','pembiayaan_risiko'
    ];

    protected static function booted()
    {
        static::saving(function ($risk) {
            $risk->skor = $risk->analisa_dampak * $risk->analisa_probabilitas * $risk->analisa_conate;
            if ($risk->skor >= 20) {
                $risk->peringkat_risiko = 'High';
            } elseif ($risk->skor >= 10) {
                $risk->peringkat_risiko = 'Medium';
            } else {
                $risk->peringkat_risiko = 'Low';
            }
        });
    }
}
