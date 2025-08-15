<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobSeekerAuthController;
use App\Http\Controllers\CompanyAuthController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobSeekerController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 🌟 最初の選択画面（要件定義書通り）
Route::get('/', [HomeController::class, 'selection'])->name('welcome');

// 求人関連（認証不要）
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');

// 求職者認証関連
Route::prefix('job_seeker')->name('job_seeker.')->group(function () {
    // 認証不要ルート
    Route::get('/login', [JobSeekerAuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [JobSeekerAuthController::class, 'login'])->name('login');
    Route::get('/register', [JobSeekerAuthController::class, 'showRegisterForm'])->name('register.form');
    
    // 🔥 修正: 確認画面表示用ルート
    Route::post('/register/confirm', [JobSeekerAuthController::class, 'showRegisterConfirm'])->name('register.confirm');
    
    // 🔥 修正: 最終登録処理用ルート
    Route::post('/register/execute', [JobSeekerAuthController::class, 'register'])->name('register.execute');
    
    // 🔥 修正: パスワードリセット関連ルート（ルート名統一）
    Route::get('/password/reset', [JobSeekerAuthController::class, 'showPasswordResetForm'])->name('password.request');
    Route::post('/password/email', [JobSeekerAuthController::class, 'sendPasswordResetEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [JobSeekerAuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/password/reset', [JobSeekerAuthController::class, 'resetPassword'])->name('password.update');
    
    // 認証必要ルート
    Route::middleware('auth:job_seeker')->group(function () {
        Route::post('/logout', [JobSeekerAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [JobSeekerController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile/edit', [JobSeekerController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [JobSeekerController::class, 'updateProfile'])->name('profile.update');
        Route::get('/withdraw', [JobSeekerController::class, 'showWithdrawForm'])->name('withdraw.form');
        Route::delete('/withdraw', [JobSeekerController::class, 'withdraw'])->name('withdraw');
        
        // 応募関連
        Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/jobs/{job}/apply', [ApplicationController::class, 'create'])->name('jobs.apply');
        Route::post('/jobs/{job}/apply', [ApplicationController::class, 'store'])->name('applications.store');
        
        // ブックマーク関連（Ajax専用 - 確認画面なし）
        Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
        Route::delete('/bookmarks/id/{bookmark}', [BookmarkController::class, 'destroyById'])->name('bookmarks.destroy.by.id');
        
        // Ajax用ブックマークAPI（メイン機能）
        Route::prefix('api')->name('api.')->group(function () {
            Route::post('/bookmarks/{job}/add', [BookmarkController::class, 'add'])->name('bookmarks.add');
            Route::delete('/bookmarks/{job}/remove', [BookmarkController::class, 'remove'])->name('bookmarks.remove');
            Route::get('/bookmarks/{job}/check', [BookmarkController::class, 'check'])->name('bookmarks.check');
        });
    });
});

// 企業認証関連
Route::prefix('company')->name('company.')->group(function () {
    // 認証不要ルート
    Route::get('/login', [CompanyAuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [CompanyAuthController::class, 'login'])->name('login');
    Route::get('/register', [CompanyAuthController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/register/confirm', [CompanyAuthController::class, 'showRegisterConfirm'])->name('register.confirm');
    Route::post('/register', [CompanyAuthController::class, 'register'])->name('register');
    Route::get('/password/reset', [CompanyAuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [CompanyAuthController::class, 'sendResetLink'])->name('password.send');
    
    // 認証必要ルート
    Route::middleware('auth:company')->group(function () {
        Route::post('/logout', [CompanyAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [CompanyController::class, 'dashboard'])->name('dashboard');
        
        Route::get('/profile', [CompanyController::class, 'showProfile'])->name('profile');
        Route::put('/profile', [CompanyController::class, 'updateProfile'])->name('profile.update');

        // 求人管理
        Route::get('/jobs', [CompanyController::class, 'jobsIndex'])->name('jobs.index');
        Route::get('/jobs/create', [CompanyController::class, 'createJob'])->name('jobs.create');
        Route::post('/jobs', [CompanyController::class, 'storeJob'])->name('jobs.store');
        Route::get('/jobs/{job}', [CompanyController::class, 'showJob'])->name('jobs.show');
        Route::get('/jobs/{job}/edit', [CompanyController::class, 'editJob'])->name('jobs.edit');
        Route::put('/jobs/{job}', [CompanyController::class, 'updateJob'])->name('jobs.update');
        Route::delete('/jobs/{job}', [CompanyController::class, 'destroyJob'])->name('jobs.destroy');
        
        // 応募者管理
        Route::get('/jobs/{job}/applications', [CompanyController::class, 'jobApplications'])->name('jobs.applications');
        Route::get('/applications/{application}', [CompanyController::class, 'showApplication'])->name('applications.show');
        Route::put('/applications/{application}/status', [CompanyController::class, 'updateApplicationStatus'])->name('applications.update.status');
    });
});

// 🌟 既存リンク対応用（後方互換性）
Route::get('/home', function () {
    return redirect()->route('welcome');
})->name('home');

// フォールバックルート（404対応）
/*
Route::fallback(function () {
    return redirect('/')->with('error', 'ページが見つかりませんでした。');
});
*/