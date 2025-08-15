<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'seeker_id', 
        'motivation',
        'email',
        'phone',
        'status',
        'applied_at'
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'status' => 'integer'
    ];

    // ステータス定数
    const STATUS_APPLIED = 0;    // 応募済
    const STATUS_SCREENING = 1;  // 選考中
    const STATUS_NOTIFIED = 2;   // 結果通知済

    // ステータス名取得メソッド（PHP7.4対応版）
    public function getStatusNameAttribute()
    {
        switch($this->status) {
            case self::STATUS_APPLIED:
                return '応募済';
            case self::STATUS_SCREENING:
                return '選考中';
            case self::STATUS_NOTIFIED:
                return '結果通知済';
            default:
                return '不明';
        }
    }

    // 求人情報とのリレーション
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    // 求職者とのリレーション 
    public function jobSeeker()
    {
        return $this->belongsTo(JobSeeker::class, 'seeker_id');
    }
}