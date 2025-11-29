<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table): void {
                if (Schema::hasColumn('orders', 'shop_table_id')) {
                    $table->dropForeign(['shop_table_id']);
                    $table->dropColumn('shop_table_id');
                }
            });
        }

        if (Schema::hasTable('carts')) {
            Schema::table('carts', function (Blueprint $table): void {
                if (Schema::hasColumn('carts', 'shop_table_id')) {
                    $table->dropForeign(['shop_table_id']);
                    $table->dropColumn('shop_table_id');
                }
            });
        }

        Schema::dropIfExists('shop_tables');
    }

    public function down(): void
    {
        if (! Schema::hasTable('shop_tables')) {
            Schema::create('shop_tables', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('slug')->unique();
                $table->uuid('qr_token')->unique();
                $table->unsignedInteger('capacity')->default(4);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('orders') && ! Schema::hasColumn('orders', 'shop_table_id')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->foreignId('shop_table_id')->nullable()->after('customer_phone')->constrained('shop_tables')->nullOnDelete();
            });
        }

        if (Schema::hasTable('carts') && ! Schema::hasColumn('carts', 'shop_table_id')) {
            Schema::table('carts', function (Blueprint $table): void {
                $table->foreignId('shop_table_id')->nullable()->after('session_token')->constrained('shop_tables')->nullOnDelete();
            });
        }
    }
};
