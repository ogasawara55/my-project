<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'location',
        'salary_range',
        'employment_type',
        'image_url',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * リレーション: Company（所属企業）
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * リレーション: Applications（応募）
     */
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * リレーション: Bookmarks（ブックマーク）
     */
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * 検索スコープ: キーワード検索
     * 求人タイトル、仕事内容、企業名を対象
     */
    public function scopeSearch(Builder $query, $keyword)
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%")
              ->orWhereHas('company', function($companyQuery) use ($keyword) {
                  $companyQuery->where('company_name', 'like', "%{$keyword}%");
              });
        });
    }

    /**
     * 検索スコープ: 勤務地フィルタ
     */
    public function scopeByLocation(Builder $query, $location)
    {
        if (empty($location)) {
            return $query;
        }

        return $query->where('location', $location);
    }

    /**
     * 検索スコープ: 雇用形態フィルタ
     */
    public function scopeByEmploymentType(Builder $query, $employmentType)
    {
        if (empty($employmentType)) {
            return $query;
        }

        return $query->where('employment_type', $employmentType);
    }

    /**
     * 検索スコープ: 給与レンジフィルタ
     */
    public function scopeBySalaryRange(Builder $query, $salaryRange)
    {
        if (empty($salaryRange)) {
            return $query;
        }

        return $query->where('salary_range', $salaryRange);
    }

    /**
     * 検索スコープ: 複合検索
     * 複数の検索条件を一度に適用
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        return $query
            ->when($filters['keyword'] ?? null, function($q, $keyword) {
                return $q->search($keyword);
            })
            ->when($filters['search'] ?? null, function($q, $search) {
                // 下位互換性のため
                return $q->search($search);
            })
            ->when($filters['location'] ?? null, function($q, $location) {
                return $q->byLocation($location);
            })
            ->when($filters['employment_type'] ?? null, function($q, $employmentType) {
                return $q->byEmploymentType($employmentType);
            })
            ->when($filters['salary_range'] ?? null, function($q, $salaryRange) {
                return $q->bySalaryRange($salaryRange);
            });
    }

    /**
     * 検索スコープ: 並び順
     */
    public function scopeSortBy(Builder $query, $sortBy = 'created_at', $sortOrder = 'desc')
    {
        switch ($sortBy) {
            case 'title':
                return $query->orderBy('title', $sortOrder);
            case 'company':
                return $query
                    ->join('companies', 'jobs.company_id', '=', 'companies.id')
                    ->orderBy('companies.company_name', $sortOrder)
                    ->select('jobs.*');
            case 'location':
                return $query->orderBy('location', $sortOrder);
            case 'salary_range':
                return $query->orderBy('salary_range', $sortOrder);
            case 'employment_type':
                return $query->orderBy('employment_type', $sortOrder);
            default:
                return $query->orderBy('created_at', $sortOrder);
        }
    }

    /**
     * 検索スコープ: 企業別フィルタ
     */
    public function scopeByCompany(Builder $query, $companyId)
    {
        if (empty($companyId)) {
            return $query;
        }

        return $query->where('company_id', $companyId);
    }

    /**
     * 検索スコープ: 最近投稿された求人
     */
    public function scopeRecent(Builder $query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * 検索スコープ: 人気の求人（応募数基準）
     */
    public function scopePopular(Builder $query, $limit = 10)
    {
        return $query->withCount('applications')
                    ->orderBy('applications_count', 'desc')
                    ->limit($limit);
    }

    /**
     * 検索スコープ: 関連求人（同じ企業の他の求人）
     */
    public function scopeRelated(Builder $query, $jobId, $companyId, $limit = 5)
    {
        return $query->where('company_id', $companyId)
                    ->where('id', '!=', $jobId)
                    ->latest()
                    ->limit($limit);
    }

    /**
     * アクセサ: 新着求人かどうか
     */
    public function getIsNewAttribute()
    {
        return $this->created_at->diffInDays(now()) <= 7;
    }

    /**
     * アクセサ: 応募数
     */
    public function getApplicationCountAttribute()
    {
        return $this->applications()->count();
    }

    /**
     * アクセサ: ブックマーク数
     */
    public function getBookmarkCountAttribute()
    {
        return $this->bookmarks()->count();
    }

    /**
     * 特定のユーザーがブックマークしているか
     */
    public function isBookmarkedBy($seekerId)
    {
        return $this->bookmarks()->where('seeker_id', $seekerId)->exists();
    }

    /**
     * 特定のユーザーが応募しているか
     */
    public function isAppliedBy($seekerId)
    {
        return $this->applications()->where('seeker_id', $seekerId)->exists();
    }

    /**
     * 静的メソッド: 利用可能な勤務地一覧
     */
    public static function getAvailableLocations()
    {
        return static::distinct('location')
                    ->whereNotNull('location')
                    ->pluck('location')
                    ->filter()
                    ->sort()
                    ->values();
    }

    /**
     * 静的メソッド: 利用可能な雇用形態一覧
     */
    public static function getAvailableEmploymentTypes()
    {
        return static::distinct('employment_type')
                    ->whereNotNull('employment_type')
                    ->pluck('employment_type')
                    ->filter()
                    ->sort()
                    ->values();
    }

    /**
     * 静的メソッド: 利用可能な給与レンジ一覧
     */
    public static function getAvailableSalaryRanges()
    {
        return static::distinct('salary_range')
                    ->whereNotNull('salary_range')
                    ->pluck('salary_range')
                    ->filter()
                    ->sort()
                    ->values();
    }

    /**
     * 静的メソッド: 検索結果の件数を取得
     */
    public static function getSearchCount(array $filters)
    {
        return static::filter($filters)->count();
    }

    /**
     * 静的メソッド: 人気の求人を取得
     */
    public static function getPopularJobs($limit = 10)
    {
        return static::with('company')
                    ->withCount('applications')
                    ->orderBy('applications_count', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * 静的メソッド: 最新の求人を取得
     */
    public static function getRecentJobs($limit = 10)
    {
        return static::with('company')
                    ->latest()
                    ->limit($limit)
                    ->get();
    }
}