<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Application;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class JobController extends Controller
{
    /**
     * 求人一覧を表示
     */
    public function index(Request $request)
    {
        try {
            // 検索条件を取得
            $filters = [
                'search' => $request->input('search'),
                'location' => $request->input('location'),
                'employment_type' => $request->input('employment_type'),
                'salary_range' => $request->input('salary_range'),
            ];

            // 求人を検索・取得
            $jobs = Job::with(['company'])
                      ->filter($filters)
                      ->sortBy('created_at', 'desc')
                      ->paginate(12);

            // 検索フィルター用のデータを取得
            $locations = Job::getAvailableLocations();
            $employmentTypes = Job::getAvailableEmploymentTypes();
            $salaryRanges = Job::getAvailableSalaryRanges();

            // ログイン中の求職者の応募・ブックマーク状態を取得
            $userApplications = collect();
            $userBookmarks = collect();
            
            if (Auth::guard('job_seeker')->check()) {
                $seekerId = Auth::guard('job_seeker')->id();
                
                // ユーザーの応募済み求人IDを配列で取得
                $userApplications = Application::where('seeker_id', $seekerId)
                                               ->pluck('job_id')
                                               ->toArray();
                
                // ユーザーのブックマーク済み求人IDを配列で取得
                $userBookmarks = Bookmark::where('seeker_id', $seekerId)
                                        ->pluck('job_id')
                                        ->toArray();
            }

            return view('jobs.index', compact(
                'jobs',
                'filters',
                'locations',
                'employmentTypes',
                'salaryRanges',
                'userApplications',
                'userBookmarks'
            ));

        } catch (\Exception $e) {
            Log::error('求人一覧取得エラー: ' . $e->getMessage());
            return back()->with('error', 'エラーが発生しました。');
        }
    }

    /**
     * 求人詳細を表示
     */
    public function show(Job $job)
    {
        try {
            // 企業情報も含めて取得
            $job->load(['company', 'applications', 'bookmarks']);

            // 関連求人を取得（同じ企業の他の求人）
            $relatedJobs = Job::where('company_id', $job->company_id)
                             ->where('id', '!=', $job->id)
                             ->with('company')
                             ->latest()
                             ->limit(6)
                             ->get();

            // 統計情報を取得
            $applicationCount = $job->applications()->count();
            $bookmarkCount = $job->bookmarks()->count();
            $companyJobsCount = Job::where('company_id', $job->company_id)->count();

            // ログイン中の求職者の状態をチェック
            $isApplied = false;
            $isBookmarked = false;

            if (Auth::guard('job_seeker')->check()) {
                $seekerId = Auth::guard('job_seeker')->id();
                
                // 応募状態をチェック
                $isApplied = Application::where('seeker_id', $seekerId)
                                       ->where('job_id', $job->id)
                                       ->exists();
                
                // ブックマーク状態をチェック
                $isBookmarked = Bookmark::where('seeker_id', $seekerId)
                                       ->where('job_id', $job->id)
                                       ->exists();
            }

            return view('jobs.show', compact(
                'job',
                'relatedJobs',
                'applicationCount',
                'bookmarkCount',
                'companyJobsCount',
                'isApplied',
                'isBookmarked'
            ));

        } catch (\Exception $e) {
            Log::error('求人詳細取得エラー: ' . $e->getMessage());
            return redirect()->route('jobs.index')->with('error', '求人が見つかりませんでした。');
        }
    }

    /**
     * 検索候補を取得（Ajax用）
     */
    public function searchSuggestions(Request $request)
    {
        try {
            $keyword = $request->input('keyword');
            
            if (empty($keyword) || strlen($keyword) < 2) {
                return response()->json([]);
            }

            // 求人タイトル、企業名から候補を取得
            $jobTitles = Job::where('title', 'like', "%{$keyword}%")
                           ->distinct()
                           ->pluck('title')
                           ->take(5);

            $companyNames = Job::with('company')
                              ->whereHas('company', function($query) use ($keyword) {
                                  $query->where('company_name', 'like', "%{$keyword}%");
                              })
                              ->get()
                              ->pluck('company.company_name')
                              ->unique()
                              ->take(5);

            $suggestions = $jobTitles->merge($companyNames)->unique()->values();

            return response()->json($suggestions);

        } catch (\Exception $e) {
            Log::error('検索候補取得エラー: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * 人気の求人を取得（Ajax用）
     */
    public function getPopularJobs(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            
            $popularJobs = Job::with(['company'])
                             ->withCount(['applications', 'bookmarks'])
                             ->orderByDesc('applications_count')
                             ->orderByDesc('bookmarks_count')
                             ->limit($limit)
                             ->get();

            return response()->json([
                'success' => true,
                'jobs' => $popularJobs->map(function($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'company_name' => $job->company->company_name,
                        'location' => $job->location,
                        'salary_range' => $job->salary_range,
                        'employment_type' => $job->employment_type,
                        'applications_count' => $job->applications_count,
                        'bookmarks_count' => $job->bookmarks_count,
                        'image_url' => $job->image_url,
                        'url' => route('jobs.show', $job->id),
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('人気求人取得エラー: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました。'
            ], 500);
        }
    }

    /**
     * 最新の求人を取得（Ajax用）
     */
    public function getRecentJobs(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            
            $recentJobs = Job::with(['company'])
                            ->latest()
                            ->limit($limit)
                            ->get();

            return response()->json([
                'success' => true,
                'jobs' => $recentJobs->map(function($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'company_name' => $job->company->company_name,
                        'location' => $job->location,
                        'salary_range' => $job->salary_range,
                        'employment_type' => $job->employment_type,
                        'created_at' => $job->created_at->format('Y/m/d'),
                        'image_url' => $job->image_url,
                        'url' => route('jobs.show', $job->id),
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('最新求人取得エラー: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました。'
            ], 500);
        }
    }

    /**
     * 求人の統計情報を取得（Ajax用）
     */
    public function getJobStats(Job $job)
    {
        try {
            $stats = [
                'applications_count' => $job->applications()->count(),
                'bookmarks_count' => $job->bookmarks()->count(),
                'views_count' => $job->views_count ?? 0, // 今後実装予定
                'is_new' => $job->created_at->diffInDays(now()) <= 7,
            ];

            // ログイン中ユーザーの状態
            if (Auth::guard('job_seeker')->check()) {
                $seekerId = Auth::guard('job_seeker')->id();
                $stats['is_applied'] = Application::where('seeker_id', $seekerId)
                                                 ->where('job_id', $job->id)
                                                 ->exists();
                $stats['is_bookmarked'] = Bookmark::where('seeker_id', $seekerId)
                                                 ->where('job_id', $job->id)
                                                 ->exists();
            }

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('求人統計取得エラー: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました。'
            ], 500);
        }
    }
}