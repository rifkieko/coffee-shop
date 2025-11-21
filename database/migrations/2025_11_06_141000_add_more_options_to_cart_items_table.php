<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (! Schema::hasColumn('cart_items', 'size')) {
                $table->string('size', 16)->nullable()->after('ice_level');
            }

            if (! Schema::hasColumn('cart_items', 'beans')) {
                $table->string('beans', 32)->nullable()->after('size');
            }

            if (! Schema::hasColumn('cart_items', 'milk_option')) {
                $table->string('milk_option', 32)->nullable()->after('beans');
            }
        });

        $this->dropCartItemForeignKeys();
        $this->dropIndexIfExists('cart_items', 'cart_items_unique_variant');
        $this->dropIndexIfExists('cart_items', 'cart_items_cart_id_menu_item_id_unique');
        $this->dropIndexIfExists('cart_items', 'cart_items_unique_variant_v2');

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['cart_id', 'menu_item_id', 'temperature', 'sugar_level', 'ice_level', 'size', 'beans', 'milk_option'], 'cart_items_unique_variant_v2');
        });

        $this->addCartItemForeignKeys();
    }

    public function down(): void
    {
        // Drop the v2 unique if exists and restore previous one
        $this->dropCartItemForeignKeys();
        $this->dropIndexIfExists('cart_items', 'cart_items_unique_variant_v2');

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['cart_id', 'menu_item_id', 'temperature', 'sugar_level', 'ice_level'], 'cart_items_unique_variant');

            $columns = collect(['size', 'beans', 'milk_option'])
                ->filter(fn (string $column) => Schema::hasColumn('cart_items', $column))
                ->all();

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });

        $this->addCartItemForeignKeys();
    }

    private function dropCartItemForeignKeys(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            $constraints = DB::select('
                SELECT CONSTRAINT_NAME as constraint_name
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_TYPE = "FOREIGN KEY"
                  AND TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = "cart_items"
            ');

            foreach ($constraints as $constraint) {
                DB::statement("ALTER TABLE `cart_items` DROP FOREIGN KEY `{$constraint->constraint_name}`");
            }

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE cart_items DROP CONSTRAINT IF EXISTS cart_items_cart_id_foreign');
            DB::statement('ALTER TABLE cart_items DROP CONSTRAINT IF EXISTS cart_items_menu_item_id_foreign');

            return;
        }

        throw new \RuntimeException("Unsupported database driver [$driver] while dropping cart_items foreign keys.");
    }

    private function addCartItemForeignKeys(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreign('cart_id')->references('id')->on('carts')->cascadeOnDelete();
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->restrictOnDelete();
        });
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            $exists = DB::select('
                SELECT 1
                FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?
                  AND INDEX_NAME = ?
                LIMIT 1
            ', [$table, $indexName]);

            if (! empty($exists)) {
                DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$indexName}`");
            }

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("DROP INDEX IF EXISTS {$indexName}");

            return;
        }

        throw new \RuntimeException("Unsupported database driver [$driver] while dropping index [$indexName].");
    }
};
