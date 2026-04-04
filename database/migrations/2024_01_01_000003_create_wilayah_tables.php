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
        // Tabel Provinsi (Level 1)
        Schema::create('reg_provinces', function (Blueprint $table) {
            $table->char('id', 2)->primary();
            $table->string('name', 255);
        });

        // Tabel Kota/Kabupaten (Level 2)
        Schema::create('reg_regencies', function (Blueprint $table) {
            $table->char('id', 4)->primary();
            $table->char('province_id', 2);
            $table->string('name', 255);
            
            // Foreign key constraint
            $table->foreign('province_id')
                  ->references('id')
                  ->on('reg_provinces')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        // Tabel Kecamatan (Level 3)
        Schema::create('reg_districts', function (Blueprint $table) {
            $table->char('id', 6)->primary();
            $table->char('regency_id', 4);
            $table->string('name', 255);
            
            // Foreign key constraint
            $table->foreign('regency_id')
                  ->references('id')
                  ->on('reg_regencies')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        // Tabel Kelurahan (Level 4)
        Schema::create('reg_villages', function (Blueprint $table) {
            $table->char('id', 10)->primary();
            $table->char('district_id', 6);
            $table->string('name', 255);
            
            // Foreign key constraint
            $table->foreign('district_id')
                  ->references('id')
                  ->on('reg_districts')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order (child tables first)
        Schema::dropIfExists('reg_villages');
        Schema::dropIfExists('reg_districts');
        Schema::dropIfExists('reg_regencies');
        Schema::dropIfExists('reg_provinces');
    }
};
