<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Risk extends Model
{
    protected $fillable = [
        'nama_kegiatan', 'tujuan_kegiatan', 'area_lokasi', 'kode_risiko', 'risiko',
        'sebab_1','sebab_2','sebab_3','sebab_4','sebab_5',
        'dampak','pernyataan_risiko','pengendalian_saat_ini',
        'analisa_dampak','analisa_probabilitas','analisa_concate','skor','peringkat_risiko',
        'perlu_penanganan','hindari_risiko','cegah_kerugian','reduksi_kerugian','segregasi',
        'contractual_transfer','rencana_penanganan','pembiayaan_risiko',
        'tipe_risiko','year','company_id','division_id',
        'status_persetujuan','ditinjau_oleh','ditinjau_pada',
        'dibuat_oleh', 'alasan_penolakan',

    ];

    protected $casts = [
        'ditinjau_pada' => 'datetime',
    ];

    protected static function booted()
    {
        // Hitung skor & peringkat otomatis
        static::saving(function ($risk) {
            $dampak = $risk->analisa_dampak ?? 0;
            $probabilitas = $risk->analisa_probabilitas ?? 0;

            $risk->skor = $dampak * $probabilitas;

            if ($risk->skor >= 15) {
                $risk->peringkat_risiko = 'Sangat Tinggi';
            } elseif ($risk->skor >= 10) {
                $risk->peringkat_risiko = 'Tinggi';
            } elseif ($risk->skor >= 5) {
                $risk->peringkat_risiko = 'Sedang';
            } elseif ($risk->skor >= 3) {
                $risk->peringkat_risiko = 'Rendah';
            } elseif ($risk->skor >= 1) {
                $risk->peringkat_risiko = 'Sangat Rendah';
            } else {
                $risk->peringkat_risiko = '-';
            }
        });

        // Isi otomatis siapa yang membuat
        static::creating(function ($risk) {
            if (auth()->check() && empty($risk->dibuat_oleh)) {
                $risk->dibuat_oleh = auth()->id();
            }
        });
    }

    // Relasi company
    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    // Relasi division
    public function division(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Division::class);
    }

    // Relasi user yang meninjau / approve / reject
    public function ditinjauOleh(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'ditinjau_oleh');
    }

    // Relasi user yang membuat
    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh');
    }
}
