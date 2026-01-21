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
        Schema::connection('main')->create('roles', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('role_name');
            $table->string('description');
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::connection('main')->create('study_programs', function (Blueprint $table) {
            $table->id('program_id');
            $table->string('program_name');
            $table->string('description');
            $table->string('db_connection');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::connection('main')->create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('nomor_induk')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('first_login')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('study_program_id')->constrained('study_programs','program_id');
            $table->string('photo')->nullable();
            $table->string('phone')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::connection('main')->create('user_roles', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('role_id')->constrained('roles','role_id');
            $table->foreignId('user_id')->constrained('users','user_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::connection('main')->create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::connection('main')->create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('main')->dropIfExists('users');
        Schema::connection('main')->dropIfExists('password_reset_tokens');
        Schema::connection('main')->dropIfExists('sessions');
        Schema::connection('main')->dropIfExists('roles');
        Schema::connection('main')->dropIfExists('user_roles');
        Schema::connection('main')->dropIfExists('study_programs');
    }
};
