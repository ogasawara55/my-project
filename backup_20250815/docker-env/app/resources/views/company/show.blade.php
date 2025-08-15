<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $job->title }} - {{ $job->company->company_name }} | 求人応募ポータル</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .company-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.8rem;
        }
        .job-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .info-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }
        .info-item {
            padding: 1rem 0;
            border-bottom: 1px solid #f8f9fa;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            min-width: 120px;
        }
        .apply-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 2rem;
            margin: 2rem 0;
        }
        .btn-apply {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 1.1rem;
        }
        .btn-apply:hover {
            background: linear-gradient(135deg, #218838 0%, #1bb789 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }
        .badge-custom {
            font-size: 0.9rem;
            padding: 8px 16px;
            border-radius: 20px;
        }
        .btn-bookmark {
            transition: all 0.3s ease;
            position: relative;
        }
        .btn-bookmark:hover {
            transform: translateY(-2px);
        }
        .btn-bookmark:disabled {
            opacity: 0.6;
            transform: none;
        }
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        .debug-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            font-family: monospace;
            font-size: 0.875rem;
            display: none;
        }
    </style>
</head>
<body class="bg-light">
    <!-- ナビゲーションバー -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <i class="fas fa-briefcase me-2"></i>求人ポータル
            </a>
            <div class="navbar-nav ms-auto">
                @auth('job_seeker')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>{{ auth('job_seeker')->user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('job_seeker.dashboard') }}">ダッシュボード</a></li>
                            <li><a class="dropdown-item" href="{{ route('job_seeker.bookmarks.index') }}">ブックマーク</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('job_seeker.logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">ログアウト</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <a class="nav-link" href="{{ route('job_seeker.login.form') }}">
                        <i class="fas fa-sign-in-alt me-1"></i>ログイン
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- デバッグ情報（開発時のみ表示） -->
    @if(config('app.debug'))
    <div class="container mt-3">
        <div class="debug-info" id="debugInfo">
            <h6>🔍 デバッグ情報</h6>
            <p><strong>Job ID:</strong> {{ $job->id }}</p>
            <p><strong>User ID:</strong> {{ auth('job_seeker')->id() ?? 'ログインしていません' }}</p>
            <p><strong>CSRF Token:</strong> <span id="csrfDisplay"></span></p>
            <p><strong>API Endpoints:</strong></p>
            <ul>
                <li>Check: /job_seeker/bookmarks/check/{{ $job->id }}</li>
                <li>Add: /job_seeker/bookmarks/add/{{ $job->id }}</li>
                <li>Remove: /job_seeker/bookmarks/remove/{{ $job->id }}</li>
            </ul>
            <button class="btn btn-sm btn-secondary" onclick="toggleDebug()">デバッグ情報を隠す</button>
        </div>
        <button class="btn btn-sm btn-info" onclick="toggleDebug()" id="showDebugBtn">🔍 デバッグ情報を表示</button>
    </div>
    @endif

    <!-- 求人ヘッダー -->
    <section class="job-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="company-logo">
                        {{ substr($job->company->company_name, 0, 1) }}
                    </div>
                </div>
                <div class="col">
                    <h1 class="display-5 fw-bold mb-2">{{ $job->title }}</h1>
                    <h4 class="mb-3">{{ $job->company->company_name }}</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge badge-custom bg-light text-dark">
                            <i class="fas fa-map-marker-alt me-1"></i>{{ $job->location }}
                        </span>
                        <span class="badge badge-custom bg-light text-dark">
                            <i class="fas fa-clock me-1"></i>{{ $job->employment_type }}
                        </span>
                        @if($job->salary_range)
                            <span class="badge badge-custom bg-light text-dark">
                                <i class="fas fa-yen-sign me-1"></i>{{ $job->salary_range }}
                            </span>
                        @endif
                        <span class="badge badge-custom bg-light text-dark">
                            <i class="fas fa-calendar me-1"></i>{{ $job->created_at->format('Y年m月d日') }}投稿
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- メインコンテンツ -->
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- 求人詳細 -->
                <div class="card info-card mb-4">
                    <div class="card-body">
                        <h3 class="card-title mb-4">
                            <i class="fas fa-info-circle text-primary me-2"></i>求人詳細
                        </h3>
                        <div class="job-description">
                            {!! nl2br(e($job->description)) !!}
                        </div>
                    </div>
                </div>

                <!-- 求人情報 -->
                <div class="card info-card mb-4">
                    <div class="card-body">
                        <h3 class="card-title mb-4">
                            <i class="fas fa-list text-primary me-2"></i>求人情報
                        </h3>
                        
                        <div class="info-item d-flex">
                            <div class="info-label">職種</div>
                            <div class="flex-fill">{{ $job->title }}</div>
                        </div>
                        
                        <div class="info-item d-flex">
                            <div class="info-label">勤務地</div>
                            <div class="flex-fill">{{ $job->location }}</div>
                        </div>
                        
                        <div class="info-item d-flex">
                            <div class="info-label">雇用形態</div>
                            <div class="flex-fill">
                                <span class="badge bg-success">{{ $job->employment_type }}</span>
                            </div>
                        </div>
                        
                        @if($job->salary_range)
                            <div class="info-item d-flex">
                                <div class="info-label">給与</div>
                                <div class="flex-fill">
                                    <span class="fw-bold text-primary">{{ $job->salary_range }}</span>
                                </div>
                            </div>
                        @endif
                        
                        <div class="info-item d-flex">
                            <div class="info-label">投稿日</div>
                            <div class="flex-fill">{{ $job->created_at->format('Y年m月d日') }}</div>
                        </div>
                    </div>
                </div>

                <!-- 企業情報 -->
                <div class="card info-card mb-4">
                    <div class="card-body">
                        <h3 class="card-title mb-4">
                            <i class="fas fa-building text-primary me-2"></i>企業情報
                        </h3>
                        
                        <div class="info-item d-flex">
                            <div class="info-label">企業名</div>
                            <div class="flex-fill fw-bold">{{ $job->company->company_name }}</div>
                        </div>
                        
                        <div class="info-item d-flex">
                            <div class="info-label">担当者</div>
                            <div class="flex-fill">{{ $job->company->contact_name }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- サイドバー -->
            <div class="col-lg-4">
                <!-- 応募セクション -->
                <div class="apply-section">
                    <h4 class="mb-3">
                        <i class="fas fa-paper-plane text-success me-2"></i>この求人に応募する
                    </h4>
                    
                    @auth('job_seeker')
                        <p class="text-muted mb-3">
                            ログイン中：{{ auth('job_seeker')->user()->name }}
                        </p>
                        <button type="button" class="btn btn-apply w-100 mb-3">
                            <i class="fas fa-paper-plane me-2"></i>応募する
                        </button>
                        <!-- ✅ ブックマークボタン -->
                        <button type="button" class="btn btn-outline-secondary w-100 btn-bookmark" id="bookmarkBtn" data-job-id="{{ $job->id }}">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="bookmarkSpinner"></span>
                            <i class="far fa-heart me-2" id="bookmarkIcon"></i>
                            <span id="bookmarkText">ブックマーク</span>
                        </button>
                        
                        <!-- デバッグボタン（開発時のみ） -->
                        @if(config('app.debug'))
                        <button type="button" class="btn btn-outline-info w-100 mt-2" onclick="testBookmarkAPI()">
                            🔧 API テスト
                        </button>
                        @endif
                    @else
                        <p class="text-muted mb-3">
                            応募するにはログインが必要です
                        </p>
                        <a href="{{ route('job_seeker.login.form') }}" class="btn btn-apply w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>ログインして応募
                        </a>
                        <a href="{{ route('job_seeker.register.form') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-user-plus me-2"></i>新規登録
                        </a>
                    @endauth
                </div>

                <!-- 類似求人 -->
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-search text-primary me-2"></i>類似の求人
                        </h5>
                        <p class="text-muted">同じ企業や職種の求人を探す</p>
                        <a href="{{ route('jobs.index', ['search' => $job->company->company_name]) }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                            <i class="fas fa-building me-1"></i>{{ $job->company->company_name }}の他の求人
                        </a>
                        <a href="{{ route('jobs.index', ['location' => $job->location]) }}" class="btn btn-outline-info btn-sm w-100">
                            <i class="fas fa-map-marker-alt me-1"></i>{{ $job->location }}の求人
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 戻るボタン -->
        <div class="row mt-4 mb-5">
            <div class="col-12">
                <a href="{{ route('jobs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>求人一覧に戻る
                </a>
            </div>
        </div>
    </div>

    <!-- トースト通知コンテナ -->
    <div class="toast-container" id="toastContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ 改善されたブックマーク機能JavaScript -->
    @auth('job_seeker')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 ブックマーク機能を初期化中...');
        
        // CSRFトークンを設定
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // デバッグ表示（開発時のみ）
        if (document.getElementById('csrfDisplay')) {
            document.getElementById('csrfDisplay').textContent = csrfToken.substring(0, 20) + '...';
        }
        
        const bookmarkBtn = document.getElementById('bookmarkBtn');
        const bookmarkIcon = document.getElementById('bookmarkIcon');
        const bookmarkText = document.getElementById('bookmarkText');
        const bookmarkSpinner = document.getElementById('bookmarkSpinner');
        
        if (!bookmarkBtn) {
            console.error('❌ ブックマークボタンが見つかりません');
            return;
        }
        
        const jobId = bookmarkBtn.getAttribute('data-job-id');
        console.log('📋 Job ID:', jobId);
        
        // 初期状態をチェック
        checkBookmarkStatus();
        
        // ブックマークボタンのクリックイベント
        bookmarkBtn.addEventListener('click', function() {
            console.log('🖱️ ブックマークボタンがクリックされました');
            
            const isBookmarked = bookmarkIcon.classList.contains('fas');
            console.log('📍 現在の状態:', isBookmarked ? 'ブックマーク済み' : '未ブックマーク');
            
            if (isBookmarked) {
                removeBookmark();
            } else {
                addBookmark();
            }
        });
        
        // ブックマーク状態をチェック
        function checkBookmarkStatus() {
            console.log('🔍 ブックマーク状態をチェック中...');
            
            fetch(`/job_seeker/bookmarks/check/${jobId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                console.log('📡 状態チェックレスポンス:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('📊 状態チェック結果:', data);
                if (data.success && data.bookmarked) {
                    setBookmarkedState();
                } else {
                    setUnbookmarkedState();
                }
            })
            .catch(error => {
                console.error('❌ 状態チェックエラー:', error);
                setUnbookmarkedState();
                showToast(`状態チェックエラー: ${error.message}`, 'warning');
            });
        }
        
        // ブックマークを追加
        function addBookmark() {
            console.log('➕ ブックマーク追加処理開始...');
            setLoadingState(true);
            
            fetch(`/job_seeker/bookmarks/add/${jobId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                console.log('📡 追加レスポンス:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('📊 追加結果:', data);
                if (data.success) {
                    setBookmarkedState();
                    showToast('ブックマークに追加しました', 'success');
                } else {
                    showToast(data.message || 'エラーが発生しました', 'error');
                }
            })
            .catch(error => {
                console.error('❌ 追加エラー:', error);
                showToast(`追加エラー: ${error.message}`, 'error');
            })
            .finally(() => {
                setLoadingState(false);
            });
        }
        
        // ブックマークを削除
        function removeBookmark() {
            console.log('➖ ブックマーク削除処理開始...');
            setLoadingState(true);
            
            fetch(`/job_seeker/bookmarks/remove/${jobId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                console.log('📡 削除レスポンス:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('📊 削除結果:', data);
                if (data.success) {
                    setUnbookmarkedState();
                    showToast('ブックマークから削除しました', 'success');
                } else {
                    showToast(data.message || 'エラーが発生しました', 'error');
                }
            })
            .catch(error => {
                console.error('❌ 削除エラー:', error);
                showToast(`削除エラー: ${error.message}`, 'error');
            })
            .finally(() => {
                setLoadingState(false);
            });
        }
        
        // ローディング状態の設定
        function setLoadingState(loading) {
            bookmarkBtn.disabled = loading;
            if (loading) {
                bookmarkSpinner.classList.remove('d-none');
                bookmarkIcon.classList.add('d-none');
            } else {
                bookmarkSpinner.classList.add('d-none');
                bookmarkIcon.classList.remove('d-none');
            }
        }
        
        // ブックマーク済み状態に設定
        function setBookmarkedState() {
            bookmarkIcon.classList.remove('far');
            bookmarkIcon.classList.add('fas');
            bookmarkText.textContent = 'ブックマーク済み';
            bookmarkBtn.classList.remove('btn-outline-secondary');
            bookmarkBtn.classList.add('btn-warning');
            console.log('✅ ブックマーク済み状態に設定');
        }
        
        // ブックマーク未済状態に設定
        function setUnbookmarkedState() {
            bookmarkIcon.classList.remove('fas');
            bookmarkIcon.classList.add('far');
            bookmarkText.textContent = 'ブックマーク';
            bookmarkBtn.classList.remove('btn-warning');
            bookmarkBtn.classList.add('btn-outline-secondary');
            console.log('⭕ ブックマーク未済状態に設定');
        }
        
        // トースト通知を表示
        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'danger'} alert-dismissible fade show`;
            toast.style.minWidth = '300px';
            toast.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.getElementById('toastContainer').appendChild(toast);
            
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 5000);
        }
        
        // グローバル関数（デバッグ用）
        window.testBookmarkAPI = function() {
            console.log('🧪 API テスト開始...');
            checkBookmarkStatus();
        };
    });
    
    // デバッグ情報の表示/非表示切り替え
    function toggleDebug() {
        const debugInfo = document.getElementById('debugInfo');
        const showBtn = document.getElementById('showDebugBtn');
        
        if (debugInfo.style.display === 'none' || debugInfo.style.display === '') {
            debugInfo.style.display = 'block';
            if (showBtn) showBtn.style.display = 'none';
        } else {
            debugInfo.style.display = 'none';
            if (showBtn) showBtn.style.display = 'block';
        }
    }
    </script>
    @endauth
</body>
</html>