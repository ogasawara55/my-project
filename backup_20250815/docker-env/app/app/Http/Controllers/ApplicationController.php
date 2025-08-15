<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    /**
     * 応募履歴一覧を表示
     */
    public function index()
    {
        try {
            $seekerId = Auth::guard('job_seeker')->id();
            
            if (!$seekerId) {
                return redirect()->route('job_seeker.login.form')->with('error', 'ログインが必要です。');
            }

            // 応募履歴を取得（ページネーション対応）
            $applications = Application::where('seeker_id', $seekerId)
                                      ->with(['job.company'])
                                      ->orderBy('applied_at', 'desc')
                                      ->paginate(10);

            // 統計情報を取得
            $totalApplications = Application::where('seeker_id', $seekerId)->count();
            $pendingApplications = Application::where('seeker_id', $seekerId)->where('status', 0)->count();
            $inProgressApplications = Application::where('seeker_id', $seekerId)->where('status', 1)->count();
            $completedApplications = Application::where('seeker_id', $seekerId)->where('status', 2)->count();

            return view('job_seeker.applications.index', compact(
                'applications',
                'totalApplications',
                'pendingApplications',
                'inProgressApplications',
                'completedApplications'
            ));

        } catch (\Exception $e) {
            Log::error('応募履歴取得エラー: ' . $e->getMessage());
            return back()->with('error', 'エラーが発生しました。');
        }
    }

    /**
     * 応募フォームを表示
     */
    public function create(Job $job)
    {
        try {
            $seekerId = Auth::guard('job_seeker')->id();
            
            if (!$seekerId) {
                return redirect()->route('job_seeker.login.form')->with('error', 'ログインが必要です。');
            }

            // 既に応募しているかチェック
            $existingApplication = Application::where('seeker_id', $seekerId)
                                             ->where('job_id', $job->id)
                                             ->first();

            if ($existingApplication) {
                return redirect()->route('jobs.show', $job->id)
                              ->with('info', 'この求人には既に応募済みです。');
            }

            // 求人情報を取得（企業情報も含む）
            $job->load('company');

            // ユーザー情報を取得
            $jobSeeker = Auth::guard('job_seeker')->user();

            return view('job_seeker.applications.create', compact('job', 'jobSeeker'));

        } catch (\Exception $e) {
            Log::error('応募フォーム表示エラー: ' . $e->getMessage());
            return back()->with('error', 'エラーが発生しました。');
        }
    }

    /**
     * 応募を送信
     */
    public function store(Request $request, Job $job)
    {
        try {
            $seekerId = Auth::guard('job_seeker')->id();
            
            if (!$seekerId) {
                return redirect()->route('job_seeker.login.form')->with('error', 'ログインが必要です。');
            }

            // 重複応募チェック
            $existingApplication = Application::where('seeker_id', $seekerId)
                                             ->where('job_id', $job->id)
                                             ->first();

            if ($existingApplication) {
                return redirect()->route('jobs.show', $job->id)
                              ->with('info', 'この求人には既に応募済みです。');
            }

            // バリデーション
            $validated = $request->validate([
                'motivation' => 'required|string|max:2000',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
            ], [
                'motivation.required' => '志望動機は必須です。',
                'motivation.max' => '志望動機は2000文字以内で入力してください。',
                'email.required' => 'メールアドレスは必須です。',
                'email.email' => '正しいメールアドレス形式で入力してください。',
                'email.max' => 'メールアドレスは255文字以内で入力してください。',
                'phone.required' => '電話番号は必須です。',
                'phone.max' => '電話番号は20文字以内で入力してください。',
            ]);

            // 応募を作成
            $application = Application::create([
                'job_id' => $job->id,
                'seeker_id' => $seekerId,
                'motivation' => $validated['motivation'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'status' => 0, // 応募済み
                'applied_at' => now(),
            ]);

            // 成功メッセージと共にリダイレクト
            return redirect()->route('jobs.show', $job->id)
                           ->with('success', '応募を送信しました。企業からの連絡をお待ちください。');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('応募送信エラー: ' . $e->getMessage());
            return back()->with('error', '応募の送信に失敗しました。')->withInput();
        }
    }

    /**
     * 応募詳細を表示
     */
    public function show(Application $application)
    {
        try {
            $seekerId = Auth::guard('job_seeker')->id();
            
            if (!$seekerId) {
                return redirect()->route('job_seeker.login.form')->with('error', 'ログインが必要です。');
            }

            // 自分の応募かチェック
            if ($application->seeker_id !== $seekerId) {
                return redirect()->route('job_seeker.applications.index')
                              ->with('error', '権限がありません。');
            }

            // 関連データを取得
            $application->load(['job.company', 'jobSeeker']);

            return view('job_seeker.applications.show', compact('application'));

        } catch (\Exception $e) {
            Log::error('応募詳細表示エラー: ' . $e->getMessage());
            return back()->with('error', 'エラーが発生しました。');
        }
    }

    /**
     * 応募をキャンセル（応募済み状態の場合のみ）
     */
    public function cancel(Application $application)
    {
        try {
            $seekerId = Auth::guard('job_seeker')->id();
            
            if (!$seekerId) {
                return redirect()->route('job_seeker.login.form')->with('error', 'ログインが必要です。');
            }

            // 自分の応募かチェック
            if ($application->seeker_id !== $seekerId) {
                return redirect()->route('job_seeker.applications.index')
                              ->with('error', '権限がありません。');
            }

            // 応募済み状態の場合のみキャンセル可能
            if ($application->status !== 0) {
                return redirect()->route('job_seeker.applications.index')
                              ->with('error', 'この応募はキャンセルできません。');
            }

            // 応募を削除
            $application->delete();

            return redirect()->route('job_seeker.applications.index')
                           ->with('success', '応募をキャンセルしました。');

        } catch (\Exception $e) {
            Log::error('応募キャンセルエラー: ' . $e->getMessage());
            return back()->with('error', '応募のキャンセルに失敗しました。');
        }
    }

    /**
     * 応募ステータスを取得（Ajax用）
     */
    public function getStatus(Application $application)
    {
        try {
            $seekerId = Auth::guard('job_seeker')->id();
            
            if (!$seekerId || $application->seeker_id !== $seekerId) {
                return response()->json([
                    'success' => false,
                    'message' => '権限がありません。'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'status' => $application->status,
                'status_text' => $this->getStatusText($application->status),
                'applied_at' => $application->applied_at->format('Y年m月d日 H:i'),
                'updated_at' => $application->updated_at->format('Y年m月d日 H:i'),
            ]);

        } catch (\Exception $e) {
            Log::error('応募ステータス取得エラー: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました。'
            ], 500);
        }
    }

    /**
     * ユーザーの応募統計を取得（Ajax用）
     */
    public function getUserApplicationStats()
    {
        try {
            $seekerId = Auth::guard('job_seeker')->id();
            
            if (!$seekerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ログインが必要です。'
                ], 401);
            }

            $stats = [
                'total' => Application::where('seeker_id', $seekerId)->count(),
                'pending' => Application::where('seeker_id', $seekerId)->where('status', 0)->count(),
                'in_progress' => Application::where('seeker_id', $seekerId)->where('status', 1)->count(),
                'completed' => Application::where('seeker_id', $seekerId)->where('status', 2)->count(),
                'this_month' => Application::where('seeker_id', $seekerId)
                                          ->whereMonth('applied_at', now()->month)
                                          ->whereYear('applied_at', now()->year)
                                          ->count(),
                'this_week' => Application::where('seeker_id', $seekerId)
                                         ->whereBetween('applied_at', [now()->startOfWeek(), now()->endOfWeek()])
                                         ->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('応募統計取得エラー: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました。'
            ], 500);
        }
    }

    /**
     * 特定の求人への応募状況をチェック（Ajax用）
     */
    public function checkApplicationStatus($jobId)
    {
        try {
            $seekerId = Auth::guard('job_seeker')->id();
            
            if (!$seekerId) {
                return response()->json([
                    'success' => false,
                    'applied' => false,
                    'message' => 'ログインが必要です。'
                ]);
            }

            $application = Application::where('seeker_id', $seekerId)
                                     ->where('job_id', $jobId)
                                     ->first();

            return response()->json([
                'success' => true,
                'applied' => !is_null($application),
                'status' => $application ? $application->status : null,
                'status_text' => $application ? $this->getStatusText($application->status) : null,
                'applied_at' => $application ? $application->applied_at->format('Y/m/d H:i') : null,
            ]);

        } catch (\Exception $e) {
            Log::error('応募状況チェックエラー: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'applied' => false,
                'message' => 'エラーが発生しました。'
            ]);
        }
    }

    /**
     * 応募ステータスのテキストを取得
     */
    private function getStatusText($status)
    {
        switch ($status) {
            case 0:
                return '応募済み';
            case 1:
                return '選考中';
            case 2:
                return '結果通知済み';
            default:
                return '不明';
        }
    }
}