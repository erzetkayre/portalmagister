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
        Schema::create('ref_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nim')->unique();
            $table->string('nama_mhs');
            $table->integer('angkatan');
            $table->string('status_mhs')->default('aktif');
            $table->integer('sks')->default(0)->nullable();
            $table->integer('ipk')->default(0)->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ref_dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('kode_dosen')->unique();
            $table->string('nip')->unique();
            $table->string('nama_dosen');
            $table->string('status_dosen')->default('aktif');
            $table->string('bidang_keahlian')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_mahasiswa');
        Schema::dropIfExists('ref_dosen');
    }
};
