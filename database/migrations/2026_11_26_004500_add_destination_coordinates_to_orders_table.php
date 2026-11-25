<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'destination_lat')) {
                $table->decimal('destination_lat', 10, 7)->nullable()->after('destination_location');
            }
            if (!Schema::hasColumn('orders', 'destination_lng')) {
                $table->decimal('destination_lng', 10, 7)->nullable()->after('destination_lat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('orders', 'destination_lat')) {
                $cols[] = 'destination_lat';
            }
            if (Schema::hasColumn('orders', 'destination_lng')) {
                $cols[] = 'destination_lng';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
