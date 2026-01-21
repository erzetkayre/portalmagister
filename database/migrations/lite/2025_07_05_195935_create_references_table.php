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
        Schema::connection('pwk')->create('ref_dosen', function (Blueprint $table) {
            $table->id('dsn_id');
            $table->integer('user_id');
            $table->string('kode_dsn')->unique()->nullable();
            $table->string('nip')->unique();
            $table->string('nama_dsn');
            $table->string('signature_dsn')->nullable();
            $table->string('status_dsn')->default('aktif');
            $table->string('bidang_keahlian')->nullable();
            $table->enum('gender', allowed: ['L', 'P'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::connection('pwk')->create('ref_mahasiswa', function (Blueprint $table) {
            $table->id('mhs_id');
            $table->integer('user_id');
            $table->string('nim')->unique();
            $table->string('nama_mhs');
            $table->integer('angkatan')->nullable();
            $table->integer('sks')->default(0)->nullable();
            $table->decimal('ipk')->default(0)->nullable();
            $table->foreignId('pem_akademik')->constrained('ref_dosen','dsn_id');
            $table->string('status_mhs')->default('aktif');
            $table->string('signature_mhs')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::connection('pwk')->create('ref_jabatan', function (Blueprint $table) {
            $table->id('jabatan_id');
            $table->foreignId('dosen_id')->constrained('ref_dosen','dsn_id')->onDelete('cascade');
            $table->string('nama_jabatan');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::connection('pwk')->create('ref_ruang', function (Blueprint $table) {
            $table->id('ruang_id');
            $table->string('nama_ruang');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::connection('pwk')->create('ref_mata_kuliah', function (Blueprint $table) {
            $table->id('mk_id');
            $table->string('nama_mk');
            $table->string('kode_mk')->nullable();
            $table->integer('sks')->default(0)->nullable();
            $table->string('status_mk')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pwk')->dropIfExists('ref_dosen');
        Schema::connection('pwk')->dropIfExists('ref_mahasiswa');
        Schema::connection('pwk')->dropIfExists('ref_jabatan');
        Schema::connection('pwk')->dropIfExists('ref_ruang');
        Schema::connection('pwk')->dropIfExists('ref_mata_kuliah');
    }
};
