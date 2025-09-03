<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('risks', function (Blueprint $table) {
            $table->dropColumn(['full_access', 'can_create', 'can_update', 'can_delete']);
        });
    }

    public function down(): void
    {
        Schema::table('risks', function (Blueprint $table) {
            $table->boolean('full_access')->default(false);
            $table->boolean('can_create')->default(false);
            $table->boolean('can_update')->default(false);
            $table->boolean('can_delete')->default(false);
        });
    }
};
