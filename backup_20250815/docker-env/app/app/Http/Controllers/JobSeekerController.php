<?php

namespace App\Http\Controllers;

use App\Models\JobSeeker;
use App\Models\Application;
use App\Models\Bookmark;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class JobSeekerController extends Controller
{
    /**
     * マイページダッシュボードを表示
     */
    public function dashboard()
    {
        try {
            $seeker = Auth::guard('job_seeker')->user();
            $seekerId = $seeker->id;

            // 統計情報を取得
            $applicationCount = Application::where('seeker_id', $seekerId)->count();
            $applicationInProgress = Application::where('seeker_id', $seekerId)
                                               ->where('status', 1) // 選考中
                                               ->count();
            $applicationCompleted = Application::where('seeker_id', $seekerId)
                                          ->where('status', 2) // 結果通知済み
                                          ->count();                 
            
            $bookmarkCount = Bookmark::where('seeker_id', $seekerId)->count();
            $notificationCount = 0; // 今後実装予定

            // 最近の応募履歴を取得（5件）
            $recentApplications = Application::where('seeker_id', $seekerId)
                                            ->with(['job.company'])
                                            ->orderBy('applied_at', 'desc')
                                            ->limit(5)
                                            ->get();

            // 最近のブックマークを取得（3件）
            $recentBookmarks = Bookmark::where('seeker_id', $seekerId)
                                      ->with(['job.company'])
                                      ->orderBy('bookmarked_at', 'desc')
                                      ->limit(3)
                                      ->get();

            return view('job_seeker.dashboard', compact(
                'seeker',
                'applicationCount',
                'applicationInProgress',
                'applicationCompleted', 
                'bookmarkCount',
                'notificationCount',
                'recentApplications',
                'recentBookmarks'
            ));

        } catch (\Exception $e) {
            Log::error('ダッシュボード表示エラー: ' . $e->getMessage());
            return back()->with('error', 'エラーが発生しました。');
        }
    }

    /**
     * プロフィール編集画面を表示
     */
    public function editProfile()
    {
        try {
            $seeker = Auth::guard('job_seeker')->user();
            return view('job_seeker.profile.edit', compact('seeker'));

        } catch (\Exception $e) {
            Log::error('プロフィール編集画面表示エラー: ' . $e->getMessage());
            return back()->with('error', 'エラーが発生しました。');
        }
    }

    /**
     * プロフィールを更新
     */
    public function updateProfile(Request $request)
    {
        try {
            $seeker = Auth::guard('job_seeker')->user();

            // バリデーション（パスワード関連を削除）
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('job_seekers')->ignore($seeker->id),
                ],
                'phone' => 'nullable|string|max:20',
                'self_pr' => 'nullable|string|max:2000',
                'career' => 'nullable|string|max:5000',
            ], [
                'name.required' => '名前は必須です。',
                'name.max' => '名前は100文字以内で入力してください。',
                'email.required' => 'メールアドレスは必須です。',
                'email.email' => '正しいメールアドレス形式で入力してください。',
                'email.unique' => 'このメールアドレスは既に使用されています。',
                'phone.max' => '電話番号は20文字以内で入力してください。',
                'self_pr.max' => '自己PRは2000文字以内で入力してください。',
                'career.max' => '職務経歴は5000文字以内で入力してください。',
            ]);

            // 基本情報を更新（パスワード処理を削除）
            $seeker->name = $validated['name'];
            $seeker->email = $validated['email'];
            $seeker->phone = $validated['phone'];
            $seeker->self_pr = $validated['self_pr'];
            $seeker->career = $validated['career'];

            $seeker->save();

            // プロフィール編集画面に留まるように変更
            return redirect()->route('job_seeker.profile.edit')
                           ->with('success', 'プロフィールを更新しました。');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('プロフィール更新エラー: ' . $e->getMessage());
            return back()->with('error', 'プロフィールの更新に失敗しました。')->withInput();
        }
    }

    /**
     * 退会確認画面を表示
     */
    public function showWithdrawForm()
    {
        try {
            $seeker = Auth::guard('job_seeker')->user();
            
            // 関連データの件数を取得
            $applicationCount = Application::where('seeker_id', $seeker->id)->count();
            $bookmarkCount = Bookmark::where('seeker_id', $seeker->id)->count();

            return view('job_seeker.withdraw', compact(
                'seeker',
                'applicationCount',
                'bookmarkCount'
            ));

        } catch (\Exception $e) {
            Log::error('退会画面表示エラー: ' . $e->getMessage());
            return back()->with('error', 'エラーが発生しました。');
        }
    }

    /**
     * 退会処理
     */
    public function withdraw(Request $request)
    {
        try {
            $seeker = Auth::guard('job_seeker')->user();

            // バリデーション
            $request->validate([
                'reason' => 'nullable|string|max:1000',
                'feedback' => 'nullable|string|max:2000',
                'confirm' => 'required|accepted',
            ], [
                'confirm.required' => '退会に同意する必要があります。',
                'confirm.accepted' => '退会に同意する必要があります。',
                'reason.max' => '退会理由は1000文字以内で入力してください。',
                'feedback.max' => 'ご意見・ご要望は2000文字以内で入力してください。',
            ]);

            // 関連データを削除
            Application::where('seeker_id', $seeker->id)->delete();
            Bookmark::where('seeker_id', $seeker->id)->delete();

            // 退会ログを記録（今後実装予定）
            Log::info('求職者退会: ' . $seeker->email . ' - 理由: ' . $request->input('reason'));

            // アカウントを削除
            $seeker->delete();

            // ログアウト
            Auth::guard('job_seeker')->logout();

            return redirect()->route('jobs.index')
                           ->with('success', '退会が完了しました。ご利用ありがとうございました。');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('退会処理エラー: ' . $e->getMessage());
            return back()->with('error', '退会処理に失敗しました。')->withInput();
        }
    }

    /**
     * 応募履歴を取得（Ajax用）
     */
    public function getApplicationHistory(Request $request)
    {
        try {
            $seekerId = Auth::guard('job_seeker')->id();
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            $applications = Application::where('seeker_id', $seekerId)
                                      ->with(['job.company'])
                                      ->orderBy('applied_at', 'desc')
                                      ->offset($offset)
                                      ->limit($limit)
                                      ->get();

            $total = Application::where('seeker_id', $seekerId)->count();

            return response()->json([
                'success' => true,
                'applications' => $applications->map(function($app) {
                    return [
                        'id' => $app->id,
                        'job_title' => $app->job->title,
                        'company_name' => $app->job->company->company_name,
                        'applied_at' => $app->applied_at->format('Y/m/d H:i'),
                        'status' => $app->status,
                        'status_text' => $this->getStatusText($app->status),
                        'job_url' => route('jobs.show', $app->job->id),
                    ];
                }),
                'total' => $total,
                'has_more' => ($offset + $limit) < $total,
            ]);

        } catch (\Exception $e) {
            Log::error('応募履歴取得エラー: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました。'
            ], 500);
        }
    }

    /**
     * ブックマーク履歴を取得（Ajax用）
     */
    public function getBookmarkHistory(Request $request)
    {
        try {
            $seekerId = Auth::guard('job_seeker')->id();
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            $bookmarks = Bookmark::where('seeker_id', $seekerId)
                                ->with(['job.company'])
                                ->orderBy('bookmarked_at', 'desc')
                                ->offset($offset)
                                ->limit($limit)
                                ->get();

            $total = Bookmark::where('seeker_id', $seekerId)->count();

            return response()->json([
                'success' => true,
                'bookmarks' => $bookmarks->map(function($bookmark) {
                    return [
                        'id' => $bookmark->id,
                        'job_title' => $bookmark->job->title,
                        'company_name' => $bookmark->job->company->company_name,
                        'bookmarked_at' => $bookmark->bookmarked_at->format('Y/m/d H:i'),
                        'job_url' => route('jobs.show', $bookmark->job->id),
                    ];
                }),
                'total' => $total,
                'has_more' => ($offset + $limit) < $total,
            ]);

        } catch (\Exception $e) {
            Log::error('ブックマーク履歴取得エラー: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました。'
            ], 500);
        }
    }

    /**
     * ユーザー統計を取得（Ajax用）
     */
    public function getUserStats()
    {
        try {
            $seekerId = Auth::guard('job_seeker')->id();

            $stats = [
                'total_applications' => Application::where('seeker_id', $seekerId)->count(),
                'pending_applications' => Application::where('seeker_id', $seekerId)->where('status', 0)->count(),
                'in_progress_applications' => Application::where('seeker_id', $seekerId)->where('status', 1)->count(),
                'completed_applications' => Application::where('seeker_id', $seekerId)->where('status', 2)->count(),
                'total_bookmarks' => Bookmark::where('seeker_id', $seekerId)->count(),
                'recent_bookmarks' => Bookmark::where('seeker_id', $seekerId)
                                            ->where('bookmarked_at', '>=', now()->subDays(7))
                                            ->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('ユーザー統計取得エラー: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました。'
            ], 500);
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