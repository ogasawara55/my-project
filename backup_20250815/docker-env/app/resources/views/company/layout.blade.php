<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '企業管理画面') - 求人応募ポータル</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="bg-light">
    <!-- 企業専用ナビゲーションバー -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('company.dashboard') }}">
                <i class="fas fa-building me-2"></i>企業管理画面
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('company.dashboard') ? 'active' : '' }}" 
                           href="{{ route('company.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>マイページ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('company.jobs.*') ? 'active' : '' }}" 
                           href="{{ route('company.jobs.index') }}">
                            <i class="fas fa-briefcase me-1"></i>求人管理
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="alert('応募者管理機能は開発中です'); return false;">
                            <i class="fas fa-users me-1"></i>応募者管理
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="companyDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-building me-1"></i>{{ Auth::guard('company')->user()->company_name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('company.profile') }}">
                                <i class="fas fa-user-edit me-2"></i>プロフィール編集
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="alert('設定機能は開発中です'); return false;">
                                <i class="fas fa-cog me-2"></i>設定
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/">
                                <i class="fas fa-home me-2"></i>トップページ
                            </a></li>
                            <li>
                                <form method="POST" action="{{ route('company.logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>ログアウト
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ブレッドクラム -->
    <nav aria-label="breadcrumb" class="bg-white border-bottom">
        <div class="container">
            <ol class="breadcrumb py-3 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('company.dashboard') }}">
                        <i class="fas fa-home"></i> ダッシュボード
                    </a>
                </li>
                @yield('breadcrumb')
            </ol>
        </div>
    </nav>

    <!-- メインコンテンツ -->
    <main class="py-4">
        <!-- フラッシュメッセージ -->
        @if (session('success'))
            <div class="container">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="container">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="container">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if (session('info'))
            <div class="container">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        <!-- ページコンテンツ -->
        @yield('content')
    </main>

    <!-- フッター -->
    <footer class="bg-dark text-white mt-auto">
        <div class="container py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small>&copy; 2025 求人応募ポータル - 企業管理画面</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <small>
                        <a href="#" class="text-white-50 me-3" onclick="alert('ヘルプ機能は開発中です'); return false;">
                            <i class="fas fa-question-circle"></i> ヘルプ
                        </a>
                        <a href="#" class="text-white-50" onclick="alert('サポート機能は開発中です'); return false;">
                            <i class="fas fa-headset"></i> サポート
                        </a>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @stack('scripts')
</body>
</html>