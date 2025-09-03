<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('division_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('division_id')
                ->constrained()
                ->onDelete('cascade');

            $table->timestamps();

            // Mencegah user punya entry duplikat di divisi yang sama
            $table->unique(['user_id', 'division_id']);
        });
    }

    public function down(): void
    {
        Schema::table('division_user', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'division_id']);
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('division_id');
        });

        Schema::dropIfExists('division_user');
    }
};
