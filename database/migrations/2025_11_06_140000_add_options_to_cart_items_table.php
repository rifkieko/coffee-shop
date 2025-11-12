<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('temperature', 10)->nullable()->after('unit_price');
            $table->unsignedTinyInteger('sugar_level')->nullable()->after('temperature');
            $table->unsignedTinyInteger('ice_level')->nullable()->after('sugar_level');
        });

        // Update unique index to also include options so variants are separated
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique('cart_items_cart_id_menu_item_id_unique');
            $table->unique(['cart_id', 'menu_item_id', 'temperature', 'sugar_level', 'ice_level'], 'cart_items_unique_variant');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique('cart_items_unique_variant');
            $table->unique(['cart_id', 'menu_item_id']);

            $table->dropColumn(['temperature', 'sugar_level', 'ice_level']);
        });
    }
};

