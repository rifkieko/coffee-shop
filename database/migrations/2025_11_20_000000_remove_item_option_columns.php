<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique('cart_items_unique_variant_v2');
            $table->dropColumn(['temperature', 'sugar_level', 'ice_level', 'size', 'beans', 'milk_option']);
            $table->unique(['cart_id', 'menu_item_id'], 'cart_items_cart_id_menu_item_id_unique');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['temperature', 'sugar_level', 'ice_level', 'size', 'beans', 'milk_option']);
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique('cart_items_cart_id_menu_item_id_unique');
            $table->string('temperature', 10)->nullable()->after('unit_price');
            $table->unsignedTinyInteger('sugar_level')->nullable()->after('temperature');
            $table->unsignedTinyInteger('ice_level')->nullable()->after('sugar_level');
            $table->string('size', 16)->nullable()->after('ice_level');
            $table->string('beans', 32)->nullable()->after('size');
            $table->string('milk_option', 32)->nullable()->after('beans');
            $table->unique(['cart_id', 'menu_item_id', 'temperature', 'sugar_level', 'ice_level', 'size', 'beans', 'milk_option'], 'cart_items_unique_variant_v2');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('temperature', 10)->nullable()->after('unit_price');
            $table->unsignedTinyInteger('sugar_level')->nullable()->after('temperature');
            $table->unsignedTinyInteger('ice_level')->nullable()->after('sugar_level');
            $table->string('size', 16)->nullable()->after('ice_level');
            $table->string('beans', 32)->nullable()->after('size');
            $table->string('milk_option', 32)->nullable()->after('beans');
        });
    }
};
