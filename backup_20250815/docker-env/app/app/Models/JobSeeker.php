<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use App\Notifications\JobSeekerResetPasswordNotification;

class JobSeeker extends Authenticatable implements CanResetPassword
{
    use HasFactory, Notifiable, CanResetPasswordTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'self_pr',
        'career',
        'phone',
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
     * 🔥 修正: hashedキャストを削除（Laravel 8以下では使用不可）
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'password' => 'hashed', // 🔥 削除: Laravel 8以下では使用不可
    ];

    /**
     * 🔥 削除: setPasswordAttribute メソッドを削除
     * 
     * 理由：パスワードリセット時に二重ハッシュ化の原因となるため
     * パスワードのハッシュ化は各コントローラーで明示的に Hash::make() を使用
     */

    /**
     * この求職者の応募
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'seeker_id');
    }

    /**
     * この求職者のブックマーク
     */
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class, 'seeker_id');
    }

    /**
     * 求職者の統計情報を取得
     */
    public function getApplicationStats()
    {
        $totalApplications = $this->applications()->count();
        $passedApplications = $this->applications()->whereIn('status', [1, 2])->count(); // 選考中・結果通知済み
        
        return [
            'total_applications' => $totalApplications,
            'passed_applications' => $passedApplications,
        ];
    }

    /**
     * 求職者の最新応募を取得
     */
    public function getRecentApplications($limit = 5)
    {
        return $this->applications()
                    ->with(['job.company'])
                    ->orderBy('applied_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * ブックマークした求人を取得
     */
    public function getBookmarkedJobs()
    {
        return $this->bookmarks()
                    ->with(['job.company'])
                    ->orderBy('bookmarked_at', 'desc')
                    ->get();
    }

    /**
     * パスワードリセット通知を送信するメールアドレスを取得（🔥 修正版）
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    /**
     * パスワードリセット通知を送信（🔥 修正版）
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new JobSeekerResetPasswordNotification($token));
    }

    /**
     * パスワードリセット用のGuard名を取得
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * RememberTokenの取得
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * RememberTokenの設定
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * RememberTokenカラム名の取得
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}