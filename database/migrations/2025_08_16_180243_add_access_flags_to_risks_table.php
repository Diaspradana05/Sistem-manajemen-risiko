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
        Schema::table('risks', function (Blueprint $table) {
            $table->boolean('full_access')->default(false)->after('updated_at');
            $table->boolean('can_create')->default(false)->after('full_access');
            $table->boolean('can_update')->default(false)->after('can_create');
            $table->boolean('can_delete')->default(false)->after('can_update');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('risks', function (Blueprint $table) {
            $table->dropColumn(['full_access', 'can_create', 'can_update', 'can_delete']);
        });
    }
};
