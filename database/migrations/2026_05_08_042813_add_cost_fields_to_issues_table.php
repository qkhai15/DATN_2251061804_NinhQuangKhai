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
        Schema::table('issues', function (Blueprint $table) {
            $table->string('responsible_party')->nullable()->after('description')->comment('Lỗi do ai');
            $table->decimal('repair_cost', 15, 2)->default(0)->after('responsible_party')->comment('Chi phí sửa chữa');
            $table->enum('payer', ['owner', 'tenant', 'shared'])->default('owner')->after('repair_cost')->comment('Ai trả chi phí');
            $table->text('admin_note')->nullable()->after('status')->comment('Ghi chú của quản trị viên');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropColumn(['responsible_party', 'repair_cost', 'payer', 'admin_note']);
        });
    }
};
