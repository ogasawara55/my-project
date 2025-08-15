<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Bookmark extends Model
{
    use HasFactory;

    /**
     * テーブル名
     */
    protected $table = 'bookmarks';

    /**
     * 主キー
     */
    protected $primaryKey = 'id';

    /**
     * 一括代入可能な属性
     */
    protected $fillable = [
        'seeker_id',
        'job_id',
        'bookmarked_at',
    ];

    /**
     * 日付型にキャストする属性
     */
    protected $casts = [
        'bookmarked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * モデル作成時のデフォルト値
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->bookmarked_at)) {
                $model->bookmarked_at = Carbon::now();
            }
        });
    }

    /**
     * この求職者のリレーション
     */
    public function jobSeeker()
    {
        return $this->belongsTo(JobSeeker::class, 'seeker_id', 'id');
    }

    /**
     * この求人のリレーション
     */
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id', 'id');
    }

    /**
     * 特定の求職者が特定の求人をブックマークしているかチェック
     */
    public static function isBookmarked($seekerId, $jobId)
    {
        return self::where('seeker_id', $seekerId)
                   ->where('job_id', $jobId)
                   ->exists();
    }

    /**
     * ブックマークを追加または削除（トグル）
     */
    public static function toggle($seekerId, $jobId)
    {
        $bookmark = self::where('seeker_id', $seekerId)
                        ->where('job_id', $jobId)
                        ->first();

        if ($bookmark) {
            // 既にブックマークされている場合は削除
            $bookmark->delete();
            return ['action' => 'removed', 'bookmarked' => false];
        } else {
            // ブックマークされていない場合は追加
            self::create([
                'seeker_id' => $seekerId,
                'job_id' => $jobId,
                'bookmarked_at' => Carbon::now(),
            ]);
            return ['action' => 'added', 'bookmarked' => true];
        }
    }

    /**
     * ブックマークを追加
     */
    public static function addBookmark($seekerId, $jobId)
    {
        // 重複チェック
        if (self::isBookmarked($seekerId, $jobId)) {
            return ['success' => false, 'message' => '既にブックマークされています'];
        }

        try {
            self::create([
                'seeker_id' => $seekerId,
                'job_id' => $jobId,
                'bookmarked_at' => Carbon::now(),
            ]);

            return ['success' => true, 'message' => 'ブックマークに追加しました'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'ブックマークの追加に失敗しました'];
        }
    }

    /**
     * ブックマークを削除
     */
    public static function removeBookmark($seekerId, $jobId)
    {
        try {
            $deleted = self::where('seeker_id', $seekerId)
                          ->where('job_id', $jobId)
                          ->delete();

            if ($deleted) {
                return ['success' => true, 'message' => 'ブックマークから削除しました'];
            } else {
                return ['success' => false, 'message' => 'ブックマークが見つかりませんでした'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'ブックマークの削除に失敗しました'];
        }
    }

    /**
     * 特定ユーザーのブックマーク一覧を取得
     */
    public static function getUserBookmarks($seekerId, $limit = null)
    {
        $query = self::with(['job.company'])
                    ->where('seeker_id', $seekerId)
                    ->orderBy('bookmarked_at', 'desc');

        if ($limit) {
            return $query->limit($limit)->get();
        }

        return $query->get();
    }

    /**
     * 特定ユーザーのブックマーク数を取得
     */
    public static function getUserBookmarkCount($seekerId)
    {
        return self::where('seeker_id', $seekerId)->count();
    }

    /**
     * 最近のブックマークを取得
     */
    public static function getRecentBookmarks($days = 7, $limit = 10)
    {
        return self::with(['job.company', 'jobSeeker'])
                   ->where('bookmarked_at', '>=', Carbon::now()->subDays($days))
                   ->orderBy('bookmarked_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    /**
     * 人気の求人（ブックマーク数順）を取得
     */
    public static function getPopularJobsByBookmarks($limit = 10)
    {
        return \App\Models\Job::withCount('bookmarks')
                             ->orderBy('bookmarks_count', 'desc')
                             ->limit($limit)
                             ->get();
    }

    /**
     * スコープ: 日付範囲でフィルタ
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('bookmarked_at', [$startDate, $endDate]);
    }

    /**
     * スコープ: 特定の求職者のブックマーク
     */
    public function scopeBySeeker($query, $seekerId)
    {
        return $query->where('seeker_id', $seekerId);
    }

    /**
     * スコープ: 特定の求人のブックマーク
     */
    public function scopeByJob($query, $jobId)
    {
        return $query->where('job_id', $jobId);
    }

    /**
     * アクセサ: ブックマーク日時の日本語表記
     */
    public function getBookmarkedAtJapaneseAttribute()
    {
        return $this->bookmarked_at->format('Y年m月d日 H:i');
    }

    /**
     * アクセサ: ブックマーク日時の相対表記
     */
    public function getBookmarkedAtRelativeAttribute()
    {
        return $this->bookmarked_at->diffForHumans();
    }
}