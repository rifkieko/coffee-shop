<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('size', 16)->nullable()->after('ice_level');
            $table->string('beans', 32)->nullable()->after('size');
            $table->string('milk_option', 32)->nullable()->after('beans');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['size', 'beans', 'milk_option']);
        });
    }
};

