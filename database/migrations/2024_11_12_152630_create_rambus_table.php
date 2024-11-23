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
        Schema::create('rambus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->nullable();
            $table->string('nama_rambu');
            $table->string('id_rambu')->nullable();
            $table->string('kategori_rambu')->nullable();
            $table->string('jenis_rambu')->nullable();
            $table->string('harga')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rambus');
    }
};
