@extends('layouts.app')

@section('title', '求人検索 - 求人応募ポータル')

{{-- ページヘッダーセクション --}}
@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">求人情報</h2>
            <p class="mt-1 text-sm text-gray-600">理想の仕事と出会う場所</p>
        </div>
        <div>
            <!-- 🌟 修正: トップページリンクを追加 -->
            <a href="{{ route('welcome') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>トップページ
            </a>
            @guest
                <a href="{{ route('job_seeker.register.form') }}" class="btn btn-primary me-2">
                    <i class="fas fa-user-plus me-1"></i>新規登録
                </a>
                <a href="{{ route('job_seeker.login.form') }}" class="btn btn-outline-primary">
                    <i class="fas fa-sign-in-alt me-1"></i>ログイン
                </a>
            @endguest
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- メインメッセージ -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="text-center py-4">
                <h1 class="display-5 fw-bold text-primary mb-3">
                    豊富な求人情報から理想の仕事を見つけよう
                </h1>
                <p class="lead text-muted mb-4">
                    様々な業界・職種の求人情報を検索できます<br>
                    気になる求人があれば、すぐに応募することができます
                </p>
            </div>
        </div>
    </div>

    <!-- 検索フォーム -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-search me-2"></i>求人検索
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('jobs.index') }}" id="searchForm">
                        <div class="row g-3">
                            <!-- キーワード検索 -->
                            <div class="col-md-6 col-lg-4">
                                <label for="keyword" class="form-label">キーワード</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control" 
                                           id="keyword" 
                                           name="keyword" 
                                           value="{{ request('keyword') }}"
                                           placeholder="求人タイトル、企業名、仕事内容">
                                </div>
                            </div>

                            <!-- 勤務地 -->
                            <div class="col-md-6 col-lg-3">
                                <label for="location" class="form-label">勤務地</label>
                                <select class="form-select" id="location" name="location">
                                    <option value="">全ての勤務地</option>
                                    <option value="東京都" {{ request('location') == '東京都' ? 'selected' : '' }}>東京都</option>
                                    <option value="大阪府" {{ request('location') == '大阪府' ? 'selected' : '' }}>大阪府</option>
                                    <option value="愛知県" {{ request('location') == '愛知県' ? 'selected' : '' }}>愛知県</option>
                                    <option value="福岡県" {{ request('location') == '福岡県' ? 'selected' : '' }}>福岡県</option>
                                    <option value="神奈川県" {{ request('location') == '神奈川県' ? 'selected' : '' }}>神奈川県</option>
                                    <option value="北海道" {{ request('location') == '北海道' ? 'selected' : '' }}>北海道</option>
                                </select>
                            </div>

                            <!-- 雇用形態 -->
                            <div class="col-md-6 col-lg-3">
                                <label for="employment_type" class="form-label">雇用形態</label>
                                <select class="form-select" id="employment_type" name="employment_type">
                                    <option value="">全ての雇用形態</option>
                                    <option value="正社員" {{ request('employment_type') == '正社員' ? 'selected' : '' }}>正社員</option>
                                    <option value="契約社員" {{ request('employment_type') == '契約社員' ? 'selected' : '' }}>契約社員</option>
                                    <option value="業務委託" {{ request('employment_type') == '業務委託' ? 'selected' : '' }}>業務委託</option>
                                    <option value="アルバイト" {{ request('employment_type') == 'アルバイト' ? 'selected' : '' }}>アルバイト</option>
                                    <option value="派遣" {{ request('employment_type') == '派遣' ? 'selected' : '' }}>派遣</option>
                                </select>
                            </div>

                            <!-- 検索ボタン -->
                            <div class="col-lg-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>検索
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 検索条件リセット -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        検索条件を指定して求人を絞り込むことができます
                                    </small>
                                    <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-undo me-1"></i>リセット
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- クイックアクション -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('jobs.index') }}" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-list me-2"></i>すべての求人を見る
                </a>
                <a href="{{ route('jobs.index') }}?sort_by=created_at&sort_order=desc" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-clock me-2"></i>新着求人
                </a>
                <a href="{{ route('jobs.index') }}?employment_type=正社員" class="btn btn-outline-info btn-lg">
                    <i class="fas fa-briefcase me-2"></i>正社員求人
                </a>
            </div>
        </div>
    </div>

    @guest
    <!-- 未ログインユーザー向けアクション -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 bg-light">
                <div class="card-body text-center py-5">
                    <h3 class="card-title mb-4">
                        <i class="fas fa-user-circle me-2"></i>アカウントを作成してさらに便利に
                    </h3>
                    <div class="row">
                        <!-- 求職者向け -->
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-user fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title">求職者として登録</h5>
                                    <p class="card-text text-muted">
                                        アカウントを作成すると...<br>
                                        • 求人への応募ができます<br>
                                        • 気になる求人をブックマークできます<br>
                                        • 応募状況を管理できます<br>
                                        • プロフィールを詳細に設定できます
                                    </p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('job_seeker.register.form') }}" class="btn btn-primary">
                                            <i class="fas fa-user-plus me-1"></i>新規登録
                                        </a>
                                        <a href="{{ route('job_seeker.login.form') }}" class="btn btn-outline-primary">
                                            <i class="fas fa-sign-in-alt me-1"></i>ログイン
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 企業向け -->
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-building fa-3x text-success mb-3"></i>
                                    <h5 class="card-title">企業として登録</h5>
                                    <p class="card-text text-muted">
                                        企業アカウントを作成すると...<br>
                                        • 求人情報を投稿できます<br>
                                        • 応募者を管理できます<br>
                                        • 選考状況を効率的に管理できます<br>
                                        • 採用統計を確認できます
                                    </p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('company.register.form') }}" class="btn btn-success">
                                            <i class="fas fa-building me-1"></i>企業登録
                                        </a>
                                        <a href="{{ route('company.login.form') }}" class="btn btn-outline-success">
                                            <i class="fas fa-sign-in-alt me-1"></i>企業ログイン
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endguest

    @auth('job_seeker')
    <!-- ログイン済み求職者向け -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 bg-primary text-white">
                <div class="card-body text-center py-4">
                    <h4 class="card-title mb-3">
                        <i class="fas fa-user-circle me-2"></i>{{ auth('job_seeker')->user()->name }}さん、理想の求人を見つけましょう
                    </h4>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('job_seeker.dashboard') }}" class="btn btn-light">
                            <i class="fas fa-tachometer-alt me-1"></i>マイページ
                        </a>
                        <a href="{{ route('job_seeker.applications.index') }}" class="btn btn-outline-light">
                            <i class="fas fa-paper-plane me-1"></i>応募履歴
                        </a>
                        <a href="{{ route('job_seeker.bookmarks.index') }}" class="btn btn-outline-light">
                            <i class="fas fa-bookmark me-1"></i>ブックマーク
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endauth

    @auth('company')
    <!-- ログイン済み企業向け -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 bg-success text-white">
                <div class="card-body text-center py-4">
                    <h4 class="card-title mb-3">
                        <i class="fas fa-building me-2"></i>{{ auth('company')->user()->company_name }}様の採用活動をサポートします
                    </h4>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('company.dashboard') }}" class="btn btn-light">
                            <i class="fas fa-tachometer-alt me-1"></i>企業管理画面
                        </a>
                        <a href="{{ route('company.jobs.create') }}" class="btn btn-outline-light">
                            <i class="fas fa-plus me-1"></i>求人投稿
                        </a>
                        <a href="{{ route('company.jobs.index') }}" class="btn btn-outline-light">
                            <i class="fas fa-list me-1"></i>求人管理
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endauth
</div>

<!-- スタイル -->
<style>
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

/* 検索フォームのレスポンシブ対応 */
@media (max-width: 768px) {
    .d-flex.gap-3.flex-wrap {
        flex-direction: column;
    }
    
    .d-flex.gap-3.flex-wrap .btn {
        margin-bottom: 0.5rem;
    }
}

/* アクセシビリティ向上 */
.form-control:focus,
.form-select:focus {
    border-color: #0066cc;
    box-shadow: 0 0 0 0.25rem rgba(0, 102, 204, 0.25);
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 検索フォームの送信時にローディング状態を表示
    const searchForm = document.getElementById('searchForm');
    const searchButton = searchForm.querySelector('button[type="submit"]');
    
    searchForm.addEventListener('submit', function() {
        searchButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>検索中...';
        searchButton.disabled = true;
    });
    
    // エンターキーでの検索
    const keywordInput = document.getElementById('keyword');
    keywordInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchForm.submit();
        }
    });
    
    // 検索条件の保存（セッションストレージ使用）
    const formInputs = searchForm.querySelectorAll('input, select');
    formInputs.forEach(input => {
        // ページロード時に保存された値を復元
        const savedValue = sessionStorage.getItem('search_' + input.name);
        if (savedValue && !input.value) {
            input.value = savedValue;
        }
        
        // 値が変更されたら保存
        input.addEventListener('change', function() {
            sessionStorage.setItem('search_' + this.name, this.value);
        });
    });
});
</script>
@endsection