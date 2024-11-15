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
        Schema::create('tiangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rambu_id')->constrained()->onDelete('cascade');
            $table->string('bentuk_tiang')->nullable();
            $table->string('tinggi_tiang')->nullable();
            $table->string('material_tiang')->nullable();
            $table->string('alat_tambahan')->nullable();
            $table->string('harga')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiangs');
    }
};
