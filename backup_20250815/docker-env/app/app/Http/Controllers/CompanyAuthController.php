<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CompanyAuthController extends Controller
{
    /**
     * ログイン画面表示
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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('company')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('company.dashboard'))
                           ->with('success', 'ログインしました');
        }

        return back()->withErrors([
            'email' => 'メールアドレスまたはパスワードが正しくありません',
        ])->onlyInput('email');
    }

    /**
     * 新規登録画面表示
     */
    public function showRegisterForm()
    {
        return view('company.auth.register');
    }

    /**
     * 新規登録確認画面表示
     */
    public function showRegisterConfirm(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:companies'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'company_name.required' => '企業名は必須です',
            'contact_name.required' => '担当者名は必須です',
            'email.required' => 'メールアドレスは必須です',
            'email.email' => '正しいメールアドレス形式で入力してください',
            'email.unique' => 'このメールアドレスは既に使用されています',
            'password.required' => 'パスワードは必須です',
            'password.min' => 'パスワードは8文字以上で入力してください',
            'password.confirmed' => 'パスワード確認が一致しません',
        ]);
        session(['company_registration_data' => $validated]);

        return view('company.auth.register-confirm', compact('validated'));
    }

    /**
     * 新規登録処理
     */
public function register(Request $request)
{
    // 🔥 セッションからデータを取得
    $validated = session('company_registration_data');
    
    if (!$validated) {
        return redirect()->route('company.register.form')
                       ->with('error', '登録データが見つかりません。最初からやり直してください。');
    }

    // 企業作成
    $company = Company::create([
        'company_name' => $validated['company_name'],
        'contact_name' => $validated['contact_name'],
        'email' => $validated['email'],
        'password' => $validated['password'], // モデルで自動ハッシュ化
    ]);

    // セッションクリア
    session()->forget('company_registration_data');

    // 自動ログイン
    Auth::guard('company')->login($company);

    return redirect()->route('company.dashboard')
                   ->with('success', '企業登録が完了しました');
}

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        Auth::guard('company')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('welcome')
                       ->with('success', 'ログアウトしました');
    }

    /**
     * パスワードリセット画面表示
     */
    public function showResetForm()
    {
        return view('company.password-reset');
    }

    /**
     * パスワードリセットリンク送信
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:companies'],
        ], [
            'email.required' => 'メールアドレスは必須です',
            'email.email' => '正しいメールアドレス形式で入力してください',
            'email.exists' => '登録されていないメールアドレスです',
        ]);

        // ここで実際のパスワードリセット処理を実装
        // 今回は簡単な成功メッセージのみ
        return back()->with('success', 'パスワードリセットリンクを送信しました');
    }
}