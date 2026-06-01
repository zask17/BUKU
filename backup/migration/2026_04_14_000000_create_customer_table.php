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
        Schema::create('customer', function (Blueprint $table) {
            $table->id('idcustomer');
            $table->string('nama_customer', 255);
            $table->text('alamat')->nullable();
            $table->unsignedBigInteger('id_provinsi')->nullable();
            $table->unsignedBigInteger('id_kota')->nullable();
            $table->unsignedBigInteger('id_kecamatan')->nullable();
            $table->unsignedBigInteger('id_kelurahan')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->longBlob('foto_blob')->nullable(); // Untuk menyimpan foto sebagai BLOB
            $table->string('foto_path', 255)->nullable(); // Untuk menyimpan path foto
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_provinsi')
                  ->references('id')
                  ->on('wilayah_provinsi')
                  ->onDelete('set null');
            $table->foreign('id_kota')
                  ->references('id')
                  ->on('wilayah_kota')
                  ->onDelete('set null');
            $table->foreign('id_kecamatan')
                  ->references('id')
                  ->on('wilayah_kecamatan')
                  ->onDelete('set null');
            $table->foreign('id_kelurahan')
                  ->references('id')
                  ->on('wilayah_kelurahan')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer');
    }
};
