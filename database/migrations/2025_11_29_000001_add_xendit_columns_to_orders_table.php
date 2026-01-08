<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('xendit_invoice_id')->nullable()->after('paid_at')->unique();
            $table->string('xendit_invoice_url')->nullable()->after('xendit_invoice_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['xendit_invoice_id', 'xendit_invoice_url']);
        });
    }
};
