<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>求職者ログイン - 求人応募ポータル</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-user me-2"></i>求職者ログイン
                        </h4>
                    </div>
                    <div class="card-body">
                        {{-- 🔥 パスワードリセット成功メッセージ（追加） --}}
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- 🔥 一般的な成功メッセージ（追加） --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- 🔥 エラーメッセージ（修正） --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>ログインエラー:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- 🔥 セッションエラーメッセージ（追加） --}}
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('job_seeker.login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope text-primary"></i> メールアドレス
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="example@email.com"
                                       required 
                                       autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock text-danger"></i> パスワード
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="パスワードを入力"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="password-toggle-icon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        <i class="fas fa-save me-1"></i>ログイン状態を保持する
                                    </label>
                                </div>
                                <div>
                                    {{-- 🔥 修正: ルート名を統一 --}}
                                    <a href="{{ route('job_seeker.password.request') }}" class="text-decoration-none small">
                                        <i class="fas fa-key me-1"></i>パスワードを忘れた方
                                    </a>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>ログイン
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <a href="{{ route('job_seeker.register.form') }}" class="text-decoration-none">
                                    <i class="fas fa-user-plus me-1"></i>アカウントをお持ちでない方はこちら
                                </a>
                            </div>
                            <div class="col-12">
                                {{-- 🔥 修正済み: route('home') → route('welcome') --}}
                                <a href="{{ route('welcome') }}" class="text-muted text-decoration-none">
                                    <i class="fas fa-arrow-left me-1"></i>トップページに戻る
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- テスト用アカウント情報 -->
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>テスト用アカウント
                        </h6>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            <strong><i class="fas fa-envelope me-1"></i>Email:</strong> suzuki@example.com<br>
                            <strong><i class="fas fa-lock me-1"></i>Password:</strong> password123
                        </small>
                        <div class="mt-2">
                            <small class="text-success">
                                <i class="fas fa-check me-1"></i>このアカウントでログインテストができます
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 企業ログインリンク -->
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="text-center">
                    <small class="text-muted">
                        企業の方は
                        <a href="{{ route('company.login.form') }}" class="text-decoration-none">
                            <i class="fas fa-building me-1"></i>企業ログインページ
                        </a>
                        からログインしてください
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- 🔥 パスワード表示切り替え機能（追加） --}}
    <script>
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('password-toggle-icon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
    
    {{-- 🔥 自動メッセージ非表示（追加） --}}
    document.addEventListener('DOMContentLoaded', function() {
        // 成功メッセージを5秒後に自動非表示
        const successAlerts = document.querySelectorAll('.alert-success');
        successAlerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });
    </script>
</body>
</html>