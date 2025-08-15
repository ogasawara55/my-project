<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '求人応募ポータル')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', Meiryo, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .main-content {
            min-height: calc(100vh - 160px);
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        
        .footer {
            background-color: #343a40;
            color: #fff;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        .btn {
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .alert {
            border: none;
            border-radius: 8px;
            border-left: 4px solid;
        }
        
        .alert-success {
            border-left-color: #28a745;
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            border-left-color: #dc3545;
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .alert-warning {
            border-left-color: #ffc107;
            background-color: #fff3cd;
            color: #856404;
        }
        
        .alert-info {
            border-left-color: #17a2b8;
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .badge {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }
        
        .form-control, .form-select {
            border-radius: 6px;
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        
        
        @media (max-width: 768px) {
            .main-content {
                padding-top: 1rem;
            }
            
            .navbar-brand {
                font-size: 1.25rem;
            }
            
            .btn {
                font-size: 0.875rem;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    

    <!-- ナビゲーション -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('jobs.index') }}">
                <i class="fas fa-briefcase me-2"></i>求人応募ポータル
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- 左側のメニュー -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}" href="{{ route('jobs.index') }}">
                            <i class="fas fa-search me-1"></i>求人検索
                        </a>
                    </li>
                </ul>
                
                <!-- 右側のメニュー -->
                <ul class="navbar-nav">
                    @auth('job_seeker')
                        <!-- 求職者ログイン時 -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="jobSeekerDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>{{ Auth::guard('job_seeker')->user()->name }}さん
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('job_seeker.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>マイページ
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('job_seeker.applications.index') }}">
                                        <i class="fas fa-paper-plane me-2"></i>応募履歴
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('job_seeker.bookmarks.index') }}">
                                        <i class="fas fa-bookmark me-2"></i>ブックマーク
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('job_seeker.profile.edit') }}">
                                        <i class="fas fa-user-edit me-2"></i>プロフィール編集
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('job_seeker.logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>ログアウト
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @elseauth('company')
                        <!-- 企業ログイン時 -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="companyDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-building me-1"></i>{{ Auth::guard('company')->user()->company_name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('company.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>管理ダッシュボード
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('company.jobs.index') }}">
                                        <i class="fas fa-briefcase me-2"></i>求人管理
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('company.jobs.create') }}">
                                        <i class="fas fa-plus me-2"></i>求人投稿
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('company.logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>ログアウト
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <!-- 未ログイン時 -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="authDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>ログイン
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header">求職者の方</h6></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('job_seeker.login.form') }}">
                                        <i class="fas fa-sign-in-alt me-2"></i>ログイン
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('job_seeker.register.form') }}">
                                        <i class="fas fa-user-plus me-2"></i>新規登録
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header">企業の方</h6></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('company.login.form') }}">
                                        <i class="fas fa-building me-2"></i>企業ログイン
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('company.register.form') }}">
                                        <i class="fas fa-plus me-2"></i>企業登録
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- メインコンテンツ -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- フッター -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-briefcase me-2"></i>求人応募ポータル</h5>
                    <p class="mb-0">あなたの転職活動をサポートします。</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="mb-2">
                        <a href="#" class="text-light me-3">プライバシーポリシー</a>
                        <a href="#" class="text-light me-3">利用規約</a>
                        <a href="#" class="text-light">お問い合わせ</a>
                    </div>
                    <small>&copy; 2025 求人応募ポータル. All rights reserved.</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    
    <!-- Global JavaScript -->
    <script>
        // CSRF トークンを設定
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ローディング表示関数
        /*function showLoading() {
            $('#loadingOverlay').show();
            $('#loadingSpinner').show();
        }

        function hideLoading() {
            $('#loadingOverlay').hide();
            $('#loadingSpinner').hide();
        }*/

        // アラート自動非表示
        $(document).ready(function() {
            $('.alert').each(function() {
                let alert = $(this);
                setTimeout(function() {
                    alert.fadeOut();
                }, 5000);
            });
        });

        // フォーム送信時のローディング表示
       /* $('form').on('submit', function() {
            // Ajaxフォームでない場合のみローディング表示
            if (!$(this).hasClass('ajax-form')) {
                showLoading();
            }
        });*/

        // ページ離脱時のローディング非表示
        $(window).on('beforeunload', function() {
            hideLoading();
        });

        // Ajax共通エラーハンドリング
        $(document).ajaxError(function(event, xhr, settings, thrownError) {
            hideLoading();
            
            let message = 'エラーが発生しました。';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            } else if (xhr.status === 401) {
                message = 'ログインが必要です。';
                window.location.href = '/job_seeker/login';
                return;
            } else if (xhr.status === 403) {
                message = '権限がありません。';
            } else if (xhr.status === 404) {
                message = 'ページが見つかりません。';
            } else if (xhr.status >= 500) {
                message = 'サーバーエラーが発生しました。';
            }
            
            showAlert(message, 'danger');
        });

        // アラート表示関数
        function showAlert(message, type = 'info') {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${getAlertIcon(type)} me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // ページ上部にアラートを挿入
            $('main.main-content').prepend(alertHtml);
            
            // 5秒後に自動で非表示
            setTimeout(function() {
                $('.alert').first().fadeOut();
            }, 5000);
        }

        // アラートアイコンを取得
        function getAlertIcon(type) {
            switch(type) {
                case 'success': return 'check-circle';
                case 'danger': return 'exclamation-circle';
                case 'warning': return 'exclamation-triangle';
                case 'info': return 'info-circle';
                default: return 'info-circle';
            }
        }

        // 確認ダイアログのヘルパー関数
        function confirmAction(message, callback) {
            if (confirm(message)) {
                callback();
            }
        }

        // 数値フォーマット関数
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // 日付フォーマット関数
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('ja-JP');
        }
    </script>
    
    @stack('scripts')
</body>
</html>