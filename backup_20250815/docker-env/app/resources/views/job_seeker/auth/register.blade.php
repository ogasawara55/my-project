@extends('layouts.app')

@section('title', '求職者登録 - 求人応募ポータル')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>求職者新規登録
                    </h3>
                    <small>あなたに最適な求人を見つけましょう</small>
                </div>
                
                <div class="card-body p-4">
                    {{-- エラーメッセージ表示 --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>入力内容にエラーがあります</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- 成功メッセージ表示 --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- ✅ フォーム（action属性を確実に修正） --}}
                    <form method="POST" action="{{ url('/job_seeker/register/confirm') }}" id="registerForm">
                        @csrf

                        {{-- 氏名 --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user text-primary"></i> 氏名 <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', 'test7') }}" 
                                   placeholder="山田太郎"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- メールアドレス --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope text-primary"></i> メールアドレス <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', 'test7@test.com') }}" 
                                   placeholder="example@email.com"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- パスワード --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock text-danger"></i> パスワード <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           value="password123"
                                           placeholder="8文字以上で入力"
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label">
                                        <i class="fas fa-lock text-danger"></i> パスワード確認 <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" 
                                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           value="password123"
                                           placeholder="上記と同じパスワードを入力"
                                           required>
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- 送信ボタン --}}
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="fas fa-arrow-right me-2"></i>確認画面へ進む
                            </button>
                        </div>

                        {{-- ログインリンク --}}
                        <div class="text-center">
                            <small class="text-muted">
                                既にアカウントをお持ちの方は 
                                <a href="{{ route('job_seeker.login.form') }}" class="text-decoration-none">
                                    <i class="fas fa-sign-in-alt me-1"></i>こちらからログイン
                                </a>
                            </small>
                        </div>
                    </form>
                </div>
            </div>

            {{-- トップページリンク --}}
            <div class="text-center mt-2">
                <a href="{{ route('welcome') }}" class="text-muted text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i>トップページに戻る
                </a>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');
    
    form.addEventListener('submit', function(e) {
        console.log('🔧 フォーム送信開始');
        console.log('送信先URL:', form.action);
        console.log('メソッド:', form.method);
        
        // ボタンの状態変更
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>送信中...';
        
        // 3秒後にボタンを戻す（デバッグ用）
        setTimeout(function() {
            if (submitBtn.disabled) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-arrow-right me-2"></i>確認画面へ進む';
                console.log('🔧 送信がタイムアウトしました');
            }
        }, 3000);
    });
});
</script>

<style>
.card {
    border: none;
    border-radius: 15px;
}

.btn-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}
</style>
@endsection