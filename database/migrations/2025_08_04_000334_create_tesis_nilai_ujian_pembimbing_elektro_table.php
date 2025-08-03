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
        Schema::create('tesis_nilai_ujian_pembimbing_elektro', function (Blueprint $table) {
            $table->id();
            $table->foreigId('tesis_pembimbing_id')->constrained('tesis_pembimbing_elektro')->cascadeOnDelete();
            $table->integer('a')->nullable();
            $table->integer('a1')->nullable();
            $table->integer('a2')->nullable();
            $table->integer('a3')->nullable();
            $table->integer('a4')->nullable();
            $table->integer('b')->nullable();
            $table->integer('b1')->nullable();
            $table->integer('b2')->nullable();
            $table->integer('b3')->nullable();
            $table->integer('b4')->nullable();
            $table->integer('c')->nullable();
            $table->integer('c1')->nullable();
            $table->integer('c2')->nullable();
            $table->integer('c3')->nullable();
            $table->integer('total')->nullable();
            $table->integer('revisi')->nullable();
            $table->integer('status_nilai')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tesis_nilai_ujian_pembimbing_elektro');
    }
};
