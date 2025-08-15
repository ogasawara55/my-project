<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\CanResetPassword;

class Company extends Authenticatable implements CanResetPassword
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_name',
        'contact_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'password' => 'hashed' を削除（Laravel 9以下では使用不可）
    ];

    /**
     * パスワードの自動ハッシュ化
     */
    public function setPasswordAttribute($password)
    {
        // 既にハッシュ化されている場合はそのまま保存
        if (strlen($password) === 60 && str_starts_with($password, '$2y$')) {
            $this->attributes['password'] = $password;
        } else {
            // 生のパスワードをハッシュ化
            $this->attributes['password'] = Hash::make($password);
        }
    }

    /**
     * この企業の求人
     */
    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    /**
     * 企業の統計情報を取得
     */
    public function getJobStats()
    {
        $totalJobs = $this->jobs()->count();
        $totalApplications = $this->jobs()->withCount('applications')->get()->sum('applications_count');
        
        return [
            'total_jobs' => $totalJobs,
            'total_applications' => $totalApplications,
        ];
    }

    /**
     * 企業の最新求人を取得
     */
    public function getRecentJobs($limit = 5)
    {
        return $this->jobs()
                    ->withCount('applications')
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * 企業の全応募を取得
     */
    public function getAllApplications()
    {
        return Application::whereHas('job', function($query) {
            $query->where('company_id', $this->id);
        })->with(['job', 'jobSeeker'])->orderBy('applied_at', 'desc');
    }
}