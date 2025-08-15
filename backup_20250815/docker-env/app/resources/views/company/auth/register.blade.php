@extends('layouts.app')

@section('title', '企業新規登録')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0 text-center">
                        <i class="fas fa-building text-success"></i> 企業新規登録
                    </h4>
                </div>

                <div class="card-body">
                    {{-- エラーメッセージ一括表示 --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>入力内容に問題があります</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('company.register.confirm') }}" novalidate>
                        @csrf

                        {{-- 企業名 --}}
                        <div class="mb-3">
                            <label for="company_name" class="form-label">
                                <i class="fas fa-building text-success"></i> 企業名 <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('company_name') is-invalid @enderror" 
                                   id="company_name" 
                                   name="company_name" 
                                   value="{{ old('company_name') }}" 
                                   placeholder="株式会社◯◯"
                                   maxlength="255"
                                   required>
                            @error('company_name')
                                <div class="invalid-feedback">
                                    <i class="fas fa-times-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- 担当者名 --}}
                        <div class="mb-3">
                            <label for="contact_name" class="form-label">
                                <i class="fas fa-user text-info"></i> 担当者名 <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('contact_name') is-invalid @enderror" 
                                   id="contact_name" 
                                   name="contact_name" 
                                   value="{{ old('contact_name') }}" 
                                   placeholder="田中太郎"
                                   maxlength="100"
                                   required>
                            @error('contact_name')
                                <div class="invalid-feedback">
                                    <i class="fas fa-times-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- メールアドレス --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope text-warning"></i> メールアドレス <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="company@example.com"
                                   maxlength="255"
                                   required>
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i> ログイン時に使用します
                            </div>
                            @error('email')
                                <div class="invalid-feedback">
                                    <i class="fas fa-times-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- パスワード --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock text-danger"></i> パスワード <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="8文字以上で入力"
                                   minlength="8"
                                   maxlength="255"
                                   required>
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i> 8文字以上で入力してください
                            </div>
                            @error('password')
                                <div class="invalid-feedback">
                                    <i class="fas fa-times-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- パスワード確認 --}}
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock text-danger"></i> パスワード確認 <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   placeholder="上記と同じパスワードを入力"
                                   minlength="8"
                                   maxlength="255"
                                   required>
                            @error('password_confirmation')
                                <div class="invalid-feedback">
                                    <i class="fas fa-times-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- 確認画面ボタン --}}
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-arrow-right"></i> 確認画面へ
                            </button>
                        </div>

                        {{-- ログインリンク --}}
                        <div class="text-center">
                            <small class="text-muted">
                                既にアカウントをお持ちの方は 
                                <a href="{{ route('company.login.form') }}" class="text-decoration-none text-primary">
                                    <i class="fas fa-sign-in-alt"></i> こちらからログイン
                                </a>
                            </small>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 求職者登録リンク --}}
            <div class="text-center mt-3">
                <div class="card bg-light">
                    <div class="card-body py-2">
                        <small class="text-muted">
                            求職者の方はこちら → 
                            <a href="{{ route('job_seeker.register.form') }}" class="text-decoration-none text-info">
                                <i class="fas fa-user"></i> 求職者登録
                            </a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- パスワード一致確認のJavaScript --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    
    function validatePasswordMatch() {
        if (password.value && passwordConfirmation.value) {
            if (password.value !== passwordConfirmation.value) {
                passwordConfirmation.setCustomValidity('パスワードが一致しません');
                passwordConfirmation.classList.add('is-invalid');
            } else {
                passwordConfirmation.setCustomValidity('');
                passwordConfirmation.classList.remove('is-invalid');
            }
        }
    }
    
    password.addEventListener('input', validatePasswordMatch);
    passwordConfirmation.addEventListener('input', validatePasswordMatch);
});
</script>
@endpush
@endsection