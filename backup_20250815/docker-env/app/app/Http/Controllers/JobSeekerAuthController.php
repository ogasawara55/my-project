<?php

namespace App\Http\Controllers;

use App\Models\JobSeeker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class JobSeekerAuthController extends Controller
{
    /**
     * ログイン画面を表示
     */
    public function showLoginForm()
    {
        // 既にログイン済みの場合はダッシュボードにリダイレクト
        if (Auth::guard('job_seeker')->check()) {
            return redirect()->route('job_seeker.dashboard');
        }

        return view('job_seeker.auth.login');
    }

    /**
     * ログイン処理
     */
    public function login(Request $request)
    {
        try {
            // バリデーション
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
                'remember' => 'boolean',
            ], [
                'email.required' => 'メールアドレスは必須です。',
                'email.email' => '正しいメールアドレス形式で入力してください。',
                'password.required' => 'パスワードは必須です。',
            ]);

            // ログイン試行
            $credentials = [
                'email' => $validated['email'],
                'password' => $validated['password'],
            ];

            $remember = $request->boolean('remember', false);

            if (Auth::guard('job_seeker')->attempt($credentials, $remember)) {
                $request->session()->regenerate();
                
                // ログイン成功
                $intendedUrl = $request->session()->pull('url.intended', route('job_seeker.dashboard'));
                
                return redirect()->to($intendedUrl)
                               ->with('success', 'ログインしました。');
            }

            // ログイン失敗
            return back()->withErrors([
                'email' => 'メールアドレスまたはパスワードが正しくありません。',
            ])->withInput($request->only('email'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('求職者ログインエラー: ' . $e->getMessage());
            return back()->with('error', 'ログインに失敗しました。')->withInput();
        }
    }

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        try {
            Auth::guard('job_seeker')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('welcome')->with('success', 'ログアウトしました。');
            
        } catch (\Exception $e) {
            Log::error('求職者ログアウトエラー: ' . $e->getMessage());
            
            // エラーが発生してもログアウト処理は継続
            Auth::guard('job_seeker')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('welcome')->with('error', 'ログアウト処理中にエラーが発生しました。');
        }
    }

    /**
     * 会員登録画面を表示
     */
    public function showRegisterForm()
    {
        // 既にログイン済みの場合はダッシュボードにリダイレクト
        if (Auth::guard('job_seeker')->check()) {
            return redirect()->route('job_seeker.dashboard');
        }

        return view('job_seeker.auth.register');
    }

    /**
     * 会員登録確認画面を表示
     */
    public function showRegisterConfirm(Request $request)
    {
        try {
            // バリデーション実行
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|max:255|unique:job_seekers,email',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'name.required' => '名前は必須です。',
                'name.max' => '名前は100文字以内で入力してください。',
                'email.required' => 'メールアドレスは必須です。',
                'email.email' => '正しいメールアドレス形式で入力してください。',
                'email.unique' => 'このメールアドレスは既に登録されています。',
                'password.required' => 'パスワードは必須です。',
                'password.min' => 'パスワードは8文字以上で入力してください。',
                'password.confirmed' => 'パスワード確認が一致しません。',
            ]);

            // セッションにデータを保存（確認画面で使用するため）
            session(['job_seeker_register_data' => $validated]);

            return view('job_seeker.auth.register_confirm', ['data' => $validated]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('求職者登録確認画面エラー: ' . $e->getMessage());
            return back()->with('error', '処理中にエラーが発生しました。')->withInput();
        }
    }

    /**
     * 会員登録処理（確認画面からの最終登録）
     */
    public function register(Request $request)
    {
        try {
            // セッションからデータを取得
            $registerData = session('job_seeker_register_data');
            
            if (!$registerData) {
                return redirect()->route('job_seeker.register.form')
                               ->with('error', 'セッションが無効です。もう一度入力してください。');
            }

            // 確認画面から送信される隠しフィールドから再度バリデーション
            $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|max:255|unique:job_seekers,email',
            ], [
                'name.required' => '名前は必須です。',
                'email.required' => 'メールアドレスは必須です。',
                'email.unique' => 'このメールアドレスは既に登録されています。',
            ]);

            // 求職者を作成
            $jobSeeker = JobSeeker::create([
                'name' => $registerData['name'],
                'email' => $registerData['email'],
                'password' => Hash::make($registerData['password']), // セッションからパスワード取得してハッシュ化
            ]);

            // セッションデータをクリア
            session()->forget('job_seeker_register_data');

            // 自動ログイン
            Auth::guard('job_seeker')->login($jobSeeker);

            return redirect()->route('job_seeker.dashboard')
                           ->with('success', 'アカウントを作成しました。プロフィールを充実させましょう。');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // バリデーションエラーの場合は登録画面に戻る
            session()->forget('job_seeker_register_data');
            return redirect()->route('job_seeker.register.form')
                           ->withErrors($e->validator)
                           ->withInput();
        } catch (\Exception $e) {
            Log::error('求職者登録エラー: ' . $e->getMessage());
            
            // セッションをクリアして登録画面に戻る
            session()->forget('job_seeker_register_data');
            return redirect()->route('job_seeker.register.form')
                           ->with('error', 'アカウントの作成に失敗しました。もう一度お試しください。');
        }
    }

    /**
     * パスワードリセット画面を表示
     */
    public function showResetForm()
    {
        return view('job_seeker.auth.password_reset');
    }

    /**
     * パスワードリセットリンクを送信
     */
    public function sendResetLink(Request $request)
    {
        try {
            // バリデーション
            $request->validate([
                'email' => 'required|email|exists:job_seekers,email',
            ], [
                'email.required' => 'メールアドレスは必須です。',
                'email.email' => '正しいメールアドレス形式で入力してください。',
                'email.exists' => 'このメールアドレスは登録されていません。',
            ]);

            // パスワードリセットリンクを送信
            $status = Password::broker('job_seekers')->sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return back()->with('status', 'パスワードリセットリンクをメールに送信しました。');
            }

            return back()->withErrors([
                'email' => 'パスワードリセットリンクの送信に失敗しました。'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('パスワードリセットエラー: ' . $e->getMessage());
            return back()->with('error', 'パスワードリセットの送信に失敗しました。')->withInput();
        }
    }

/**
     * パスワードリセット画面を表示
     */
    public function showPasswordResetForm()
    {
        return view('job_seeker.auth.password_reset');
    }

    /**
     * パスワードリセットメール送信処理
     */
    public function sendPasswordResetEmail(Request $request)
    {
        try {
            // バリデーション
            $request->validate([
                'email' => 'required|email|exists:job_seekers,email',
            ], [
                'email.required' => 'メールアドレスは必須です。',
                'email.email' => '正しいメールアドレス形式で入力してください。',
                'email.exists' => 'このメールアドレスは登録されていません。',
            ]);

            $jobSeeker = JobSeeker::where('email', $request->email)->first();
            
            if ($jobSeeker) {
                // トークン生成
                $token = Str::random(60);
                
                // password_reset_tokensテーブルに保存
                \DB::table('password_reset_tokens')->updateOrInsert(
                    ['email' => $request->email],
                    [
                        'email' => $request->email,
                        'token' => Hash::make($token),
                        'created_at' => now()
                    ]
                );

                // 通知送信
                $jobSeeker->notify(new \App\Notifications\JobSeekerResetPasswordNotification($token));

                return back()->with('status', 'パスワードリセット用のリンクをメールで送信しました。');
            }

            return back()->withErrors(['email' => 'このメールアドレスは登録されていません。']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('パスワードリセットメール送信エラー: ' . $e->getMessage());
            return back()->with('error', 'パスワードリセットの送信に失敗しました。')->withInput();
        }
    }

    /**
     * パスワードリセット実行画面を表示
     */
    public function showResetPasswordForm(Request $request, $token = null)
    {
        return view('job_seeker.auth.reset_password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * パスワードリセット実行処理
     */
    public function resetPassword(Request $request)
    {
        try {
            // バリデーション
            $request->validate([
                'token' => 'required',
                'email' => 'required|email|exists:job_seekers,email',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'email.required' => 'メールアドレスは必須です。',
                'email.email' => '正しいメールアドレス形式で入力してください。',
                'email.exists' => 'このメールアドレスは登録されていません。',
                'password.required' => 'パスワードは必須です。',
                'password.min' => 'パスワードは8文字以上で入力してください。',
                'password.confirmed' => 'パスワード確認が一致しません。',
                'token.required' => '無効なリクエストです。',
            ]);

            // トークンの検証
            $resetRecord = \DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
                return back()->withErrors(['email' => 'このパスワードリセットトークンは無効です。']);
            }

            // トークンの有効期限チェック（1時間）
            if (now()->diffInMinutes($resetRecord->created_at) > 60) {
                return back()->withErrors(['email' => 'このパスワードリセットトークンは期限切れです。']);
            }

            // ユーザー取得
            $user = JobSeeker::where('email', $request->email)->first();

            if (!$user) {
                return back()->withErrors(['email' => 'ユーザーが見つかりません。']);
            }

            // パスワード更新（remember_tokenは使用しない）
            $user->forceFill([
                'password' => Hash::make($request->password)
            ])->save();

            // リセットトークンを削除
            \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return redirect()->route('job_seeker.login.form')->with('status', 'パスワードが正常にリセットされました。新しいパスワードでログインしてください。');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('パスワード変更エラー: ' . $e->getMessage());
            return back()->with('error', 'パスワードの変更に失敗しました。もう一度お試しください。')->withInput();
        }
    }

    /**
     * ログイン状態をチェック（Ajax用）
     */
    public function checkLoginStatus()
    {
        try {
            $isLoggedIn = Auth::guard('job_seeker')->check();
            $user = null;

            if ($isLoggedIn) {
                $user = [
                    'id' => Auth::guard('job_seeker')->id(),
                    'name' => Auth::guard('job_seeker')->user()->name,
                    'email' => Auth::guard('job_seeker')->user()->email,
                ];
            }

            return response()->json([
                'success' => true,
                'logged_in' => $isLoggedIn,
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            Log::error('ログイン状態チェックエラー: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'logged_in' => false,
                'message' => 'エラーが発生しました。'
            ]);
        }
    }
}