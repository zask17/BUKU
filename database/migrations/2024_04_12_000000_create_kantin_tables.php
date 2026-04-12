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
        // Tabel Vendor
        Schema::create('vendor', function (Blueprint $table) {
            $table->id('idvendor');
            $table->string('nama_vendor', 255);
        });

        // Tabel Menu
        Schema::create('menu', function (Blueprint $table) {
            $table->id('idmenu');
            $table->string('nama_menu', 255);
            $table->integer('harga');
            $table->string('path_gambar', 255)->nullable();
            $table->unsignedBigInteger('idvendor');
            
            $table->foreign('idvendor')
                  ->references('idvendor')
                  ->on('vendor')
                  ->onDelete('cascade');
        });

        // Tabel Pesanan
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('idpesanan');
            $table->string('nama', 255);
            $table->timestamp('timestamp')->useCurrent();
            $table->integer('total');
            $table->integer('metode_bayar')->nullable();
            $table->smallInteger('status_bayar')->default(0); // 0=Pending, 1=Lunas, 2=Gagal
            $table->string('snap_token', 255)->nullable();
            $table->string('order_id_pg', 100)->unique()->nullable();
        });

        // Tabel Detail Pesanan
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id('iddetail_pesanan');
            $table->unsignedBigInteger('idmenu');
            $table->unsignedBigInteger('idpesanan');
            $table->integer('jumlah');
            $table->integer('harga');
            $table->integer('subtotal');
            $table->timestamp('timestamp')->useCurrent();
            $table->string('catatan', 255)->nullable();
            
            $table->foreign('idmenu')
                  ->references('idmenu')
                  ->on('menu');
            
            $table->foreign('idpesanan')
                  ->references('idpesanan')
                  ->on('pesanan')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('menu');
        Schema::dropIfExists('vendor');
    }
};
