<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('size', 16)->nullable()->after('ice_level');
            $table->string('beans', 32)->nullable()->after('size');
            $table->string('milk_option', 32)->nullable()->after('beans');
        });

        // In PostgreSQL unique() creates a table constraint. Drop old ones defensively.
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE "cart_items" DROP CONSTRAINT IF EXISTS "cart_items_unique_variant"'); } catch (\Throwable $e) {}
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE "cart_items" DROP CONSTRAINT IF EXISTS "cart_items_cart_id_menu_item_id_unique"'); } catch (\Throwable $e) {}

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['cart_id', 'menu_item_id', 'temperature', 'sugar_level', 'ice_level', 'size', 'beans', 'milk_option'], 'cart_items_unique_variant_v2');
        });
    }

    public function down(): void
    {
        // Drop the v2 unique if exists and restore previous one
        try { \Illuminate\Support\Facades\DB::statement('ALTER TABLE "cart_items" DROP CONSTRAINT IF EXISTS "cart_items_unique_variant_v2"'); } catch (\Throwable $e) {}
        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['cart_id', 'menu_item_id', 'temperature', 'sugar_level', 'ice_level'], 'cart_items_unique_variant');
            $table->dropColumn(['size', 'beans', 'milk_option']);
        });
    }
};
