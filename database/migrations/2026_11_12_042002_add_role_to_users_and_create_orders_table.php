<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambahkan role ke tabel users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('penyuruh')->after('email'); // 'penyuruh', 'disuruh', 'admin'
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('role');
            }
        });

        // Buat tabel orders (tugas suruh)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyuruh_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('disuruh_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->string('service_type'); // 'Antar Barang', 'Bersih-bersih', 'Belanja', 'Antre', dll
            $table->string('pickup_location')->nullable();
            $table->string('destination_location')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price')->default(20000);
            $table->enum('status', ['menunggu', 'diambil', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->timestamp('taken_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone']);
        });
    }
};
