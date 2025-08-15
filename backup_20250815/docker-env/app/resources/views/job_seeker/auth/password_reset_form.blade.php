@extends('layouts.app')

@section('title', '新しいパスワードの設定')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="auth-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h2 class="auth-title">新しいパスワード</h2>
                    <p class="auth-subtitle">新しいパスワードを設定してください</p>
                </div>

                <form method="POST" action="{{ route('job_seeker.password.update') }}" class="auth-form">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>メールアドレス
                        </label>
                        <input id="email" 
                               type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               name="email" 
                               value="{{ $email ?? old('email') }}" 
                               required 
                               autocomplete="email" 
                               readonly
                               placeholder="your@example.com">
                        @error('email')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>新しいパスワード
                        </label>
                        <div class="password-input-group">
                            <input id="password" 
                                   type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="8文字以上で入力してください">
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="password-toggle-icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                        <div class="password-requirements">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                パスワードは8文字以上で設定してください
                            </small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password-confirm" class="form-label">
                            <i class="fas fa-lock me-2"></i>パスワード確認
                        </label>
                        <div class="password-input-group">
                            <input id="password-confirm" 
                                   type="password" 
                                   class="form-control" 
                                   name="password_confirmation" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="もう一度パスワードを入力してください">
                            <button type="button" class="password-toggle" onclick="togglePassword('password-confirm')">
                                <i class="fas fa-eye" id="password-confirm-toggle-icon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-auth">
                        <i class="fas fa-save me-2"></i>
                        パスワードを更新
                    </button>
                </form>

                <div class="auth-links">
                    <div class="text-center">
                        <a href="{{ route('job_seeker.login') }}" class="auth-link">
                            <i class="fas fa-arrow-left me-1"></i>
                            ログインに戻る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-toggle-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<style>
.auth-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    padding: 3rem;
    margin-top: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.auth-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.auth-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.auth-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem auto;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.auth-icon i {
    font-size: 2rem;
    color: white;
}

.auth-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.auth-subtitle {
    color: #6c757d;
    font-size: 1rem;
    margin: 0;
}

.auth-form {
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 0.875rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    background-color: white;
}

.form-control.is-invalid {
    border-color: #dc3545;
    background-color: #fff5f5;
}

.form-control[readonly] {
    background-color: #e9ecef;
    opacity: 1;
}

.password-input-group {
    position: relative;
}

.password-input-group .form-control {
    padding-right: 3rem;
}

.password-toggle {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    padding: 0.25rem;
    transition: color 0.3s ease;
}

.password-toggle:hover {
    color: #495057;
}

.password-requirements {
    margin-top: 0.5rem;
}

.invalid-feedback {
    display: block;
    font-size: 0.875rem;
    color: #dc3545;
    margin-top: 0.5rem;
    padding-left: 0.5rem;
}

.btn-auth {
    width: 100%;
    padding: 1rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-auth:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

.btn-auth:active {
    transform: translateY(0);
}

.auth-links {
    border-top: 1px solid #e9ecef;
    padding-top: 1.5rem;
}

.auth-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.auth-link:hover {
    color: #764ba2;
    text-decoration: underline;
}

.text-muted {
    font-size: 0.9rem;
}

/* レスポンシブ対応 */
@media (max-width: 768px) {
    .auth-card {
        padding: 2rem;
        margin: 1rem;
        border-radius: 15px;
    }
    
    .auth-title {
        font-size: 1.75rem;
    }
    
    .auth-icon {
        width: 70px;
        height: 70px;
    }
    
    .auth-icon i {
        font-size: 1.75rem;
    }
}

@media (max-width: 576px) {
    .auth-card {
        padding: 1.5rem;
    }
    
    .auth-title {
        font-size: 1.5rem;
    }
}
</style>
@endsection