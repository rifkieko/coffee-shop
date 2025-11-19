<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('email', 'rifkieko@gmail.com')
            ->update(['role' => 'Customer']);
    }

    public function down(): void
    {
        // no-op: avoid downgrading role automatically
    }
};
