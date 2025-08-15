<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BookmarkController extends Controller
{
    /**
     * ブックマーク一覧を表示
     */
    public function index()
    {
        try {
            $seekerId = Auth::guard('job_seeker')->id();
            
            if (!$seekerId) {
                return redirect()->route('job_seeker.login.form')->with('error', 'ログインが必要です。');
            }

            // ブックマーク一覧を取得（ページネーション対応）
            $bookmarks = Bookmark::where('seeker_id', $seekerId)
                                ->with(['job.company'])
                                ->orderBy('bookmarked_at', 'desc')
                                ->paginate(12);

            return view('job_seeker.bookmarks.index', compact('bookmarks'));
            
        } catch (\Exception $e) {
            Log::error('ブックマーク一覧取得エラー: ' . $e->getMessage());
            return back()->with('error', 'エラーが発生しました。');
        }
    }

    /**
     * ブックマークを削除（ブックマーク一覧から）
     */
    public function destroyById(Bookmark $bookmark)
    {
        try {
            $seekerId = Auth::guard('job_seeker')->id();
            
            if (!$seekerId) {
                return redirect()->route('job_seeker.login.form')->with('error', 'ログインが必要です。');
            }

            // 自分のブックマークかチェック
            if ($bookmark->seeker_id !== $seekerId) {
                return redirect()->route('job_seeker.bookmarks.index')
                              ->with('error', '権限がありません。');
            }

            $bookmark->delete();

            return redirect()->route('job_seeker.bookmarks.index')
                          ->with('success', 'ブックマークを削除しました。');
            
        } catch (\Exception $e) {
            Log::error('ブックマークID削除エラー: ' . $e->getMessage());
            return back()->with('error', 'ブックマークの削除に失敗しました。');
        }
    }

    /**
     * ブックマークを追加（Ajax）
     */
    public function add(Request $request, $jobId)
    {
        try {
            $seekerId = Auth::guard('job_seeker')->id();
            
            if (!$seekerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ログインが必要です。'
                ], 401);
            }

            // 求人が存在するかチェック
            $job = Job::find($jobId);
            if (!$job) {
                return response()->json([
                    'success' => false,
                    'message' => '求人が見つかりません。'
                ], 404);
            }

            // 既にブックマークされているかチェック
            if (Bookmark::isBookmarked($seekerId, $jobId)) {
                return response()->json([
                    'success' => false,
                    'message' => '既にブックマークされています。'
                ], 409);
            }

            // ブックマークを追加
            $bookmark = Bookmark::create([
                'seeker_id' => $seekerId,
                'job_id' => $jobId,
                'bookmarked_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'ブックマークに追加しました。',
                'bookmark_id' => $bookmark->id
            ]);

        } catch (\Exception $e) {
            Log::error('ブックマーク追加エラー: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました。'
            ], 500);
        }
    }

    /**
     * ブックマークを削除（Ajax）
     */
    public function remove(Request $request, $jobId)
    {
        try {
            $seekerId = Auth::guard('job_seeker')->id();
            
            if (!$seekerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ログインが必要です。'
                ], 401);
            }

            // ブックマークが存在するかチェック
            if (!Bookmark::isBookmarked($seekerId, $jobId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ブックマークが見つかりません。'
                ], 404);
            }

            // ブックマークを削除
            $deleted = Bookmark::where('seeker_id', $seekerId)
                              ->where('job_id', $jobId)
                              ->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'ブックマークから削除しました。'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => '削除に失敗しました。'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('ブックマーク削除エラー: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました。'
            ], 500);
        }
    }

    /**
     * ブックマーク状態をチェック（Ajax）
     */
    public function check($jobId)
    {
        try {
            $seekerId = Auth::guard('job_seeker')->id();
            
            if (!$seekerId) {
                return response()->json([
                    'success' => false,
                    'bookmarked' => false,
                    'message' => 'ログインが必要です。'
                ]);
            }

            $isBookmarked = Bookmark::isBookmarked($seekerId, $jobId);

            return response()->json([
                'success' => true,
                'bookmarked' => $isBookmarked
            ]);

        } catch (\Exception $e) {
            Log::error('ブックマーク状態チェックエラー: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'bookmarked' => false,
                'message' => 'エラーが発生しました。'
            ]);
        }
    }
}