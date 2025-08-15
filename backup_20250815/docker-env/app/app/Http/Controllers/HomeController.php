<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Company;
use App\Models\JobSeeker;
use App\Models\Application;

class HomeController extends Controller
{
    /**
     * 要件定義書通りの最初の選択画面を表示
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function selection()
    {
        try {
            // 統計情報を取得
            $stats = [
                'total_jobs' => Job::count(),
                'total_companies' => Company::count(),
                'total_job_seekers' => JobSeeker::count(),
                'total_applications' => Application::count(),
            ];
            
            return view('index', compact('stats'));
            
        } catch (\Exception $e) {
            // エラーが発生した場合でも基本的な統計で表示
            $stats = [
                'total_jobs' => 0,
                'total_companies' => 0,
                'total_job_seekers' => 0,
                'total_applications' => 0,
            ];
            
            return view('index', compact('stats'));
        }
    }

    /**
     * 旧ホームページへのリダイレクト（後方互換性のため）
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return redirect()->route('welcome');
    }

    /**
     * 求人検索画面を表示（既存のwelcome.blade.phpを活用）
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function jobSearch()
    {
        // 必要に応じて既存のwelcome.blade.phpを使用
        return view('welcome');
    }
}