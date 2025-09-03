<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('division_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained()->onDelete('cascade');
            $table->year('year'); // langsung simpan tahun, tanpa tabel year
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('division_years');
    }
};

