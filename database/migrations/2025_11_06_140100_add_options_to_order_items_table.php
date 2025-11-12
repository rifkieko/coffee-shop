<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('temperature', 10)->nullable()->after('unit_price');
            $table->unsignedTinyInteger('sugar_level')->nullable()->after('temperature');
            $table->unsignedTinyInteger('ice_level')->nullable()->after('sugar_level');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['temperature', 'sugar_level', 'ice_level']);
        });
    }
};

