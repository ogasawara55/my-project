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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id(); // AUTO_INCREMENT主キー
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('title'); // 求人タイトル
            $table->text('description'); // 仕事内容詳細（スペル修正: descripiton → description）
            $table->string('location'); // 勤務地
            $table->string('salary_range', 100); // 給与レンジ
            $table->string('employment_type', 100); // 雇用形態
            $table->string('image_url')->nullable(); // 任意入力
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};