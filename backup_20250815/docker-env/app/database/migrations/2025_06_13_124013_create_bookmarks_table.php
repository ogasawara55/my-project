<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id(); // bookmark_id (主キー)
            $table->unsignedBigInteger('seeker_id'); // 求職者ID
            $table->unsignedBigInteger('job_id'); // 求人ID
            $table->timestamp('bookmarked_at')->useCurrent(); // ブックマーク日時
            $table->timestamps(); // created_at, updated_at
            
            // 外部キー制約
            $table->foreign('seeker_id')->references('id')->on('job_seekers')->onDelete('cascade');
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
            
            // 複合ユニークキー（同じ求職者が同じ求人を重複してブックマークできないようにする）
            $table->unique(['seeker_id', 'job_id'], 'unique_seeker_job_bookmark');
            
            // インデックス（検索パフォーマンス向上）
            $table->index(['seeker_id', 'bookmarked_at']);
            $table->index('job_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookmarks');
    }
};