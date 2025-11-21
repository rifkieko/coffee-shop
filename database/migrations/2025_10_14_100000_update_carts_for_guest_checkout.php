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

        $this->dropUserForeignConstraint();

        // Allow guest carts by making user_id nullable, then re-adding the FK with nullOnDelete
        $this->setUserIdNullable();

        Schema::table('carts', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropUserForeignConstraint();
        $this->setUserIdNotNullable();

        Schema::table('carts', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            if (Schema::hasColumn('carts', 'session_token')) {
                $table->dropColumn('session_token');
            }
        });
    }

    private function setUserIdNullable(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `carts` MODIFY `user_id` BIGINT UNSIGNED NULL');

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE carts ALTER COLUMN user_id DROP NOT NULL');

            return;
        }

        throw new \RuntimeException("Unsupported database driver [$driver] for carts.user_id alteration.");
    }

    private function setUserIdNotNullable(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `carts` MODIFY `user_id` BIGINT UNSIGNED NOT NULL');

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE carts ALTER COLUMN user_id SET NOT NULL');

            return;
        }

        throw new \RuntimeException("Unsupported database driver [$driver] for carts.user_id alteration.");
    }

    private function dropUserForeignConstraint(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            $constraints = DB::select("
                SELECT CONSTRAINT_NAME as constraint_name
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'carts'
                    AND COLUMN_NAME = 'user_id'
                    AND REFERENCED_TABLE_NAME IS NOT NULL
            ");

            foreach ($constraints as $constraint) {
                DB::statement("ALTER TABLE `carts` DROP FOREIGN KEY `{$constraint->constraint_name}`");
            }

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE carts DROP CONSTRAINT IF EXISTS carts_user_id_foreign');

            return;
        }

        throw new \RuntimeException("Unsupported database driver [$driver] for carts.user_id foreign key handling.");
    }
};
