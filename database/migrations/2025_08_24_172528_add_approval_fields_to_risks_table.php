<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('risks', function (Blueprint $table) {
            // User yang membuat risiko
            $table->unsignedBigInteger('created_by')->nullable()->after('id');

            // Approval flow
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('created_by');
            $table->unsignedBigInteger('approved_by')->nullable()->after('reviewed_by');

            // Status workflow
            $table->enum('status', ['draft', 'reviewed', 'approved', 'rejected'])->default('draft')->after('approved_by');

            // Timestamp approval
            $table->timestamp('approved_at')->nullable()->after('status');

            // Foreign key (opsional, jika pakai tabel users)
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('risks', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['reviewed_by']);
            $table->dropForeign(['approved_by']);

            $table->dropColumn(['created_by', 'reviewed_by', 'approved_by', 'status', 'approved_at']);
        });
    }
};
