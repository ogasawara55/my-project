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
        Schema::create('applications', function (Blueprint $table) {
            $table->id(); // AUTO_INCREMENT主キー
            $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
            $table->foreignId('seeker_id')->constrained('job_seekers')->onDelete('cascade');
            $table->text('motivation'); // 志望動機
            $table->string('email'); // 応募時の連絡先（入力必須）
            $table->string('phone', 20); // 応募時の連絡先（入力必須）
            $table->tinyInteger('status')->default(0); // 0=応募済/1=選考中/2=結果通知済
            $table->timestamp('applied_at')->useCurrent(); // 自動設定
            $table->timestamps();
            
            // 複合ユニークキー（同じ求人に同じ求職者が複数応募することを防ぐ）
            $table->unique(['job_id', 'seeker_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};