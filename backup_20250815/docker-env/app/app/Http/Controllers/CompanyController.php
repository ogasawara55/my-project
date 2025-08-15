<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CompanyController extends Controller
{
    /**
     * ログインフォーム表示
     */
    public function showLoginForm()
    {
        return view('company.auth.login');
    }

    /**
     * ログイン処理
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('company')->attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('company.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => ['ログイン情報が正しくありません。'],
        ]);
    }

    /**
     * 登録フォーム表示
     */
    public function showRegisterForm()
    {
        return view('company.auth.register');
    }

    /**
     * 登録確認画面表示
     */
    public function showRegisterConfirm(Request $request)
    {
        // バリデーション実行
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:companies',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // パスワードをセッションに保存（確認画面では表示しない）
        session(['register_data' => $validated]);

        return view('company.auth.register-confirm', compact('validated'));
    }

    /**
     * 登録処理
     */
    public function register(Request $request)
    {
        // セッションからデータを取得
        $registerData = session('register_data');
        
        if (!$registerData) {
            return redirect()->route('company.register.form')->with('error', 'セッションが無効です。もう一度入力してください。');
        }

        // 再度バリデーション（セキュリティのため）
        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:companies',
        ]);

        $company = Company::create([
            'company_name' => $request->company_name,
            'contact_name' => $request->contact_name,
            'email' => $request->email,
            'password' => Hash::make($registerData['password']), // セッションからパスワード取得
        ]);

        // セッションデータをクリア
        session()->forget('register_data');

        Auth::guard('company')->login($company);

        return redirect()->route('company.dashboard')->with('success', '企業登録が完了しました。');
    }

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        try {
            Auth::guard('company')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('welcome')->with('success', 'ログアウトしました。');
            
        } catch (\Exception $e) {
            Log::error('企業ログアウトエラー: ' . $e->getMessage());
            
            // エラーが発生してもログアウト処理は継続
            Auth::guard('company')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('welcome')->with('error', 'ログアウト処理中にエラーが発生しました。');
        }
    }

    /**
     * パスワードリセットフォーム表示
     */
    public function showResetForm()
    {
        return view('company.auth.reset-password');
    }

    /**
     * パスワードリセットリンク送信
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        // パスワードリセット処理の実装（今回は簡易実装）
        return back()->with('status', 'パスワードリセットリンクを送信しました。');
    }

    /**
     * ダッシュボード（求人管理画面）
     */
    public function dashboard()
    {
        $company = Auth::guard('company')->user();
        $jobs = $company->jobs()->with('applications')->get();
        
        return view('company.dashboard', compact('jobs'));
    }

    /**
     * 企業プロフィール表示
     */
    public function showProfile()
    {
        $company = Auth::guard('company')->user();
        return view('company.profile', compact('company'));
    }

    /**
     * 企業プロフィール更新
     */
    public function updateProfile(Request $request)
    {
        $company = Auth::guard('company')->user();

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:companies,email,' . $company->id,
        ]);

        $company->update($validated);

        return redirect()->route('company.profile')
            ->with('success', 'プロフィールを更新しました。');
    }

    /**
     * 求人一覧表示
     */
    public function jobsIndex()
    {
        $company = Auth::guard('company')->user();
        
        // 企業の求人一覧を取得（応募数も含む）
        $jobs = $company->jobs()
                       ->withCount([
                           'applications',
                           'applications as passed_count' => function ($query) {
                               $query->whereIn('status', [1, 2]); // 選考中・結果通知済みを通過とする
                           }
                       ])
                       ->orderBy('created_at', 'desc')
                       ->get();
        
        // 統計情報を計算
        $totalApplications = $jobs->sum('applications_count');
        $passedTotal = $jobs->sum('passed_count');
        $inProgress = $jobs->sum(function ($job) {
            return $job->applications()->where('status', 1)->count(); // 選考中のみ
        });
        
        return view('company.jobs.index', compact(
            'jobs', 
            'totalApplications', 
            'passedTotal', 
            'inProgress'
        ));
    }

    /**
     * 求人作成フォーム表示
     */
    public function createJob()
    {
        return view('company.jobs.create');
    }

    /**
     * 求人保存処理
     */
    public function storeJob(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'salary_range' => 'required|string|max:100',
            'employment_type' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $company = Auth::guard('company')->user();
        
        // 画像アップロード処理
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('job_images', 'public');
            $validated['image_url'] = $imagePath;
        }

        $company->jobs()->create($validated);

        return redirect()->route('company.jobs.index')
            ->with('success', '求人を投稿しました。');
    }

    /**
     * 求人詳細表示
     */
    public function showJob(Job $job)
    {
        // 企業の求人かチェック
        $company = Auth::guard('company')->user();
        if ($job->company_id !== $company->id) {
            abort(403, 'この求人にアクセスする権限がありません。');
        }
        
        // 応募者数も取得
        $job->loadCount([
            'applications',
            'applications as passed_count' => function ($query) {
                $query->whereIn('status', [1, 2]);
            }
        ]);
        
        return view('company.jobs.show', compact('job'));
    }

    /**
     * 求人編集フォーム表示
     */
    public function editJob(Job $job)
    {
        // 自社の求人かチェック
        if ($job->company_id !== Auth::guard('company')->id()) {
            abort(403);
        }

        return view('company.jobs.edit', compact('job'));
    }

    /**
     * 求人更新処理（Ajax対応）
     */
    public function updateJob(Request $request, Job $job)
    {
        // 企業ユーザーが自分の求人のみ編集できるかチェック
        if ($job->company_id !== Auth::guard('company')->user()->id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => '権限がありません。'
                ], 403);
            }
            abort(403);
        }

        // バリデーション
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'salary_range' => 'required|string',
            'employment_type' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'title.required' => '求人タイトルは必須です。',
            'title.max' => '求人タイトルは255文字以内で入力してください。',
            'description.required' => '仕事内容詳細は必須です。',
            'location.required' => '勤務地は必須です。',
            'location.max' => '勤務地は255文字以内で入力してください。',
            'salary_range.required' => '給与レンジは必須です。',
            'employment_type.required' => '雇用形態は必須です。',
            'image.image' => 'ファイルは画像である必要があります。',
            'image.mimes' => '画像はjpeg、png、jpg、gif形式のみ対応しています。',
            'image.max' => '画像ファイルは2MB以下にしてください。'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => '入力内容に問題があります。',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // 求人データを更新
            $updateData = [
                'title' => $request->title,
                'description' => $request->description,
                'location' => $request->location,
                'salary_range' => $request->salary_range,
                'employment_type' => $request->employment_type
            ];

            // 画像処理
            $imageUrl = null;
            if ($request->hasFile('image')) {
                // 既存の画像を削除
                if ($job->image_url && Storage::disk('public')->exists($job->image_url)) {
                    Storage::disk('public')->delete($job->image_url);
                }

                // 新しい画像を保存
                $imagePath = $request->file('image')->store('job_images', 'public');
                $updateData['image_url'] = $imagePath;
                $imageUrl = asset('storage/' . $imagePath);
            }

            // データベース更新
            $job->update($updateData);

            if ($request->ajax()) {
                $response = [
                    'success' => true,
                    'message' => '求人情報が正常に更新されました。',
                    'job' => $job->fresh() // 更新後のデータを取得
                ];

                // 画像URLが更新された場合は含める
                if ($imageUrl) {
                    $response['image_url'] = $imageUrl;
                }

                return response()->json($response);
            }

            return redirect()->route('company.jobs.edit', $job->id)
                            ->with('success', '求人情報が正常に更新されました。');

        } catch (\Exception $e) {
            Log::error('求人更新エラー: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'システムエラーが発生しました。しばらく時間をおいて再度お試しください。'
                ], 500);
            }

            return redirect()->back()
                            ->with('error', 'システムエラーが発生しました。しばらく時間をおいて再度お試しください。')
                            ->withInput();
        }
    }

    /**
     * 求人削除処理
     */
    public function destroyJob(Job $job)
    {
        // 企業の求人かチェック
        $company = Auth::guard('company')->user();
        if ($job->company_id !== $company->id) {
            abort(403, 'この求人を削除する権限がありません。');
        }
        
        // 応募がある場合は確認
        $applicationsCount = $job->applications()->count();
        if ($applicationsCount > 0) {
            return redirect()->route('company.jobs.index')
                            ->with('error', "この求人には{$applicationsCount}件の応募があるため削除できません。");
        }
        
        $jobTitle = $job->title;
        
        // 画像ファイルも削除
        if ($job->image_url && Storage::disk('public')->exists($job->image_url)) {
            Storage::disk('public')->delete($job->image_url);
        }

        $job->delete();

        return redirect()->route('company.jobs.index')
                        ->with('success', "求人「{$jobTitle}」を削除しました。");
    }

    /**
     * 求人の応募者一覧表示
     */
    public function jobApplications(Job $job)
    {
        // 自社の求人かチェック
        if ($job->company_id !== Auth::guard('company')->id()) {
            abort(403);
        }

        $applications = $job->applications()
            ->with('jobSeeker')
            ->orderBy('applied_at', 'desc')
            ->get();

        return view('company.jobs.applications', compact('job', 'applications'));
    }

    /**
     * 応募詳細表示
     */
    public function showApplication(Application $application)
    {
        // 自社の求人への応募かチェック
        if ($application->job->company_id !== Auth::guard('company')->id()) {
            abort(403);
        }

        $application->load(['job', 'jobSeeker']);

        return view('company.applications.show', compact('application'));
    }

    /**
     * 応募ステータス更新
     */
    public function updateApplicationStatus(Request $request, Application $application)
    {
        // 自社の求人への応募かチェック
        if ($application->job->company_id !== Auth::guard('company')->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|integer|in:0,1,2',
        ]);

        $application->update($validated);

        $statusTexts = [
            0 => '応募済',
            1 => '選考中',
            2 => '結果通知済'
        ];

        return back()->with('success', 
            '応募ステータスを「' . $statusTexts[$validated['status']] . '」に更新しました。'
        );
    }
}