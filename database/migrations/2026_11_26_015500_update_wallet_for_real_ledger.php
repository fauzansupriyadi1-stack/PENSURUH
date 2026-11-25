<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('balance', 14, 2)->default(0)->change();
        });

        // Reset dummy 500000 balance for users who have no SUCCESS topup transactions
        $usersWithTopup = DB::table('wallet_transactions')
            ->where('status', 'SUCCESS')
            ->pluck('user_id')
            ->unique()
            ->toArray();

        $query = DB::table('users')->where('balance', 500000);
        if (!empty($usersWithTopup)) {
            $query->whereNotIn('id', $usersWithTopup);
        }
        $query->update(['balance' => 0]);

        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('wallet_transactions', 'type')) {
                $table->string('type')->default('TOPUP')->after('user_id'); // TOPUP, ORDER_PAYMENT, REFUND, ORDER_EARNING, WITHDRAW
            }
            if (!Schema::hasColumn('wallet_transactions', 'description')) {
                $table->string('description')->nullable()->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropColumn(['type', 'description']);
        });
    }
};
