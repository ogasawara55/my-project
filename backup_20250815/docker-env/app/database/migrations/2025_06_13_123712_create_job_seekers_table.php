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
        Schema::create('job_seekers', function (Blueprint $table) {
            $table->id(); // AUTO_INCREMENT主キー
            $table->string('name', 100); // 氏名
            $table->string('email')->unique(); // 一意制約
            $table->string('password'); // ハッシュ化して保存
            $table->string('phone', 20)->nullable(); // 任意入力
            $table->text('career')->nullable(); // 任意入力
            $table->text('self_pr')->nullable(); // 任意入力
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_seekers');
    }
};