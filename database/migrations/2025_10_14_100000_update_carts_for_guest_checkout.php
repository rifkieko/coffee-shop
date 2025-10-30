<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            if (! Schema::hasColumn('carts', 'session_token')) {
                $table->uuid('session_token')->nullable()->unique()->after('user_id');
            }
        });

        // Allow user_id to be nullable and set null on delete
        DB::statement('ALTER TABLE carts DROP CONSTRAINT IF EXISTS carts_user_id_foreign');
        DB::statement('ALTER TABLE carts ALTER COLUMN user_id DROP NOT NULL');
        DB::statement('ALTER TABLE carts ADD CONSTRAINT carts_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE carts DROP CONSTRAINT IF EXISTS carts_user_id_foreign');
        DB::statement('ALTER TABLE carts ALTER COLUMN user_id SET NOT NULL');
        DB::statement('ALTER TABLE carts ADD CONSTRAINT carts_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');

        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'session_token')) {
                $table->dropColumn('session_token');
            }
        });
    }
};
