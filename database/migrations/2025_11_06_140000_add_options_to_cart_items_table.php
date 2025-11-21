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
            if (! Schema::hasColumn('cart_items', 'temperature')) {
                $table->string('temperature', 10)->nullable()->after('unit_price');
            }

            if (! Schema::hasColumn('cart_items', 'sugar_level')) {
                $table->unsignedTinyInteger('sugar_level')->nullable()->after('temperature');
            }

            if (! Schema::hasColumn('cart_items', 'ice_level')) {
                $table->unsignedTinyInteger('ice_level')->nullable()->after('sugar_level');
            }
        });

        // Update unique index to also include options so variants are separated
        $this->dropCartItemForeignKeys();
        $this->dropIndexIfExists('cart_items', 'cart_items_cart_id_menu_item_id_unique');
        $this->dropIndexIfExists('cart_items', 'cart_items_unique_variant');

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['cart_id', 'menu_item_id', 'temperature', 'sugar_level', 'ice_level'], 'cart_items_unique_variant');
        });

        $this->addCartItemForeignKeys();
    }

    public function down(): void
    {
        $this->dropCartItemForeignKeys();

        $this->dropIndexIfExists('cart_items', 'cart_items_unique_variant');

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['cart_id', 'menu_item_id']);

            $columns = collect(['temperature', 'sugar_level', 'ice_level'])
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
