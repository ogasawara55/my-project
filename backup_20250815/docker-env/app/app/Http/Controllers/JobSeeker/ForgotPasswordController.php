<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * パスワードリセットリンク要求フォームを表示
     */
    public function showLinkRequestForm()
    {
        return view('job_seeker.auth.password_reset');
    }

    /**
     * パスワードブローカーを取得
     */
    public function broker()
    {
        return Password::broker('job_seekers');
    }

    /**
     * パスワードリセットが要求された後のレスポンス
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return back()->with('status', trans($response));
    }

    /**
     * パスワードリセット要求が失敗した場合のレスポンス
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }

    /**
     * ガードを取得
     */
    protected function guard()
    {
        return auth()->guard('job_seeker');
    }
}