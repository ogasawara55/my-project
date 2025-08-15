@extends('layouts.app')

@section('title', 'マイページ')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- ウェルカムメッセージ -->
            <div class="welcome-hero mb-5">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="display-4 text-white mb-3">
                                <i class="fas fa-user-circle me-3"></i>おかえりなさい、{{ Auth::guard('job_seeker')->user()->name }}さん
                            </h1>
                            <p class="lead text-white-50">求人応募ポータルへようこそ。あなたの転職活動をサポートします。</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="welcome-illustration">
                                <i class="fas fa-briefcase fa-4x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="container">
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="container">
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            <div class="container">
                <!-- 統計情報 -->
<div class="row mb-5">
    <div class="col-md-4 col-sm-6 mb-4">
        <div class="stats-card bg-gradient-success">
            <div class="stats-icon">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div class="stats-content">
                <h3 class="stats-number">{{ $applicationCount ?? 0 }}</h3>
                <p class="stats-label">応募合計</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-4">
        <div class="stats-card bg-gradient-info">
            <div class="stats-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-content">
                <h3 class="stats-number">{{ ($applicationInProgress ?? 0) + ($applicationCompleted ?? 0) }}</h3>
                <p class="stats-label">通過合計</p>
                <small class="stats-sublabel">選考中 + 結果通知済み</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-4">
        <div class="stats-card bg-gradient-warning">
            <div class="stats-icon">
                <i class="fas fa-bookmark"></i>
            </div>
            <div class="stats-content">
                <h3 class="stats-number">{{ $bookmarkCount ?? 0 }}</h3>
                <p class="stats-label">ブックマーク合計</p>
            </div>
        </div>
    </div>
</div>

                <!-- 主要機能メニュー -->
                <div class="row mb-5">
                    <div class="col-12">
                        <h2 class="section-title mb-4">
                            <i class="fas fa-tachometer-alt me-2"></i>主要機能
                        </h2>
                    </div>
                    
                    <!-- 求人検索 -->
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="feature-card feature-card-primary">
                            <div class="feature-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="feature-content">
                                <h5 class="feature-title">求人検索</h5>
                                <p class="feature-description">新しい求人を探して、あなたに最適な職場を見つけましょう。</p>
                                <a href="{{ route('jobs.index') }}" class="btn btn-primary btn-feature">
                                    求人を探す
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 応募履歴 -->
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="feature-card feature-card-success">
                            <div class="feature-icon">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <div class="feature-content">
                                <h5 class="feature-title">応募履歴</h5>
                                <p class="feature-description">これまでの応募状況や選考結果を確認できます。</p>
                                <a href="{{ route('job_seeker.applications.index') }}" class="btn btn-success btn-feature">
                                    応募履歴を見る
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- ブックマーク -->
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="feature-card feature-card-warning">
                            <div class="feature-icon">
                                <i class="fas fa-bookmark"></i>
                            </div>
                            <div class="feature-content">
                                <h5 class="feature-title">ブックマーク</h5>
                                <p class="feature-description">気になる求人をブックマークして、後から確認できます。</p>
                                <a href="{{ route('job_seeker.bookmarks.index') }}" class="btn btn-warning btn-feature">
                                    ブックマーク一覧
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- プロフィール編集 -->
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="feature-card feature-card-info">
                            <div class="feature-icon">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            <div class="feature-content">
                                <h5 class="feature-title">プロフィール</h5>
                                <p class="feature-description">プロフィール情報を編集して、企業にアピールしましょう。</p>
                                <a href="{{ route('job_seeker.profile.edit') }}" class="btn btn-info btn-feature">
                                    プロフィール編集
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 最近の活動 -->
                @if(isset($recentApplications) && $recentApplications->count() > 0)
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="activity-section">
                                <div class="section-header">
                                    <h2 class="section-title">
                                        <i class="fas fa-history me-2"></i>最近の応募履歴
                                    </h2>
                                </div>
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>求人タイトル</th>
                                                        <th>企業名</th>
                                                        <th>応募日</th>
                                                        <th>ステータス</th>
                                                        <th>操作</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recentApplications as $application)
                                                        <tr>
                                                            <td class="fw-medium">{{ $application->job->title }}</td>
                                                            <td>{{ $application->job->company->company_name }}</td>
                                                            <td>{{ $application->applied_at->format('Y/m/d') }}</td>
                                                            <td>
                                                                @switch($application->status)
                                                                    @case(0)
                                                                        <span class="badge bg-primary">応募済み</span>
                                                                        @break
                                                                    @case(1)
                                                                        <span class="badge bg-warning">選考中</span>
                                                                        @break
                                                                    @case(2)
                                                                        <span class="badge bg-success">結果通知済み</span>
                                                                        @break
                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('jobs.show', $application->job->id) }}" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-eye me-1"></i>詳細
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center mt-4">
                                            <a href="{{ route('job_seeker.applications.index') }}" class="btn btn-primary">
                                                <i class="fas fa-list me-2"></i>すべての応募履歴を見る
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 最近のブックマーク -->
                @if(isset($recentBookmarks) && $recentBookmarks->count() > 0)
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="activity-section">
                                <div class="section-header">
                                    <h2 class="section-title">
                                        <i class="fas fa-bookmark me-2"></i>最近のブックマーク
                                    </h2>
                                </div>
                                <div class="row">
                                    @foreach($recentBookmarks as $bookmark)
                                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                            <div class="bookmark-card">
                                                @if($bookmark->job->image_url)
                                                    <div class="bookmark-image">
                                                        <img src="{{ $bookmark->job->image_url }}" alt="求人画像">
                                                    </div>
                                                @else
                                                    <div class="bookmark-image bookmark-image-placeholder">
                                                        <i class="fas fa-briefcase"></i>
                                                    </div>
                                                @endif
                                                <div class="bookmark-content">
                                                    <h6 class="bookmark-title">{{ Str::limit($bookmark->job->title, 30) }}</h6>
                                                    <p class="bookmark-company">{{ $bookmark->job->company->company_name }}</p>
                                                    <a href="{{ route('jobs.show', $bookmark->job->id) }}" class="btn btn-sm btn-primary">
                                                        詳細を見る
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-center mt-4">
                                    <a href="{{ route('job_seeker.bookmarks.index') }}" class="btn btn-warning">
                                        <i class="fas fa-bookmark me-2"></i>すべてのブックマークを見る
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ヘルプモーダル -->
<div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="helpModalLabel">
                    <i class="fas fa-question-circle me-2"></i>使い方ガイド
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="accordion" id="helpAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                求人の探し方
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <ol>
                                    <li>「求人検索」ボタンをクリック</li>
                                    <li>キーワード、勤務地、雇用形態などで絞り込み</li>
                                    <li>気になる求人の「詳細を見る」をクリック</li>
                                    <li>求人詳細を確認して応募またはブックマーク</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                応募の方法
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <ol>
                                    <li>求人詳細ページで「応募する」ボタンをクリック</li>
                                    <li>志望動機と連絡先を入力</li>
                                    <li>内容を確認して送信</li>
                                    <li>応募完了後は「応募履歴」で状況を確認</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                ブックマーク機能
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <ul>
                                    <li>気になる求人の「ブックマーク」ボタンをクリック</li>
                                    <li>ブックマークした求人は「ブックマーク一覧」で確認</li>
                                    <li>後からゆっくり検討したい求人の保存に便利</li>
                                    <li>不要になったらいつでも削除可能</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
            </div>
        </div>
    </div>
</div>

<style>
/* ウェルカムヒーローセクション */
.welcome-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 4rem 0;
    margin-top: -2rem;
    position: relative;
    overflow: hidden;
}

.welcome-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="1000,100 1000,0 0,100"/></svg>') no-repeat;
    background-size: cover;
}

.welcome-illustration {
    position: relative;
    z-index: 2;
}

/* 統計カード */
.stats-card {
    background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary-rgb) 100%);
    border-radius: 15px;
    padding: 2rem;
    color: white;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    transform: translate(30px, -30px);
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #007bff 100%) !important;
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
}

.bg-gradient-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
}

.stats-icon {
    font-size: 2.5rem;
    opacity: 0.9;
    position: relative;
    z-index: 2;
}

.stats-content {
    position: relative;
    z-index: 2;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    line-height: 1;
}

.stats-label {
    font-size: 1rem;
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
}

/* セクションタイトル */
.section-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 2rem;
}

/* 機能カード */
.feature-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--feature-color, #007bff);
}

.feature-card-primary::before { background: #007bff; }
.feature-card-success::before { background: #28a745; }
.feature-card-warning::before { background: #ffc107; }
.feature-card-info::before { background: #17a2b8; }

.feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.12);
}

.feature-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    margin-bottom: 1.5rem;
}

.feature-card-primary .feature-icon { background: #007bff; }
.feature-card-success .feature-icon { background: #28a745; }
.feature-card-warning .feature-icon { background: #ffc107; }
.feature-card-info .feature-icon { background: #17a2b8; }

.feature-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.feature-description {
    color: #6c757d;
    margin-bottom: 1.5rem;
    flex-grow: 1;
    line-height: 1.6;
}

.btn-feature {
    width: 100%;
    padding: 0.75rem;
    font-weight: 500;
    border-radius: 10px;
    transition: all 0.3s ease;
}

/* アクティビティセクション */
.activity-section {
    margin-bottom: 3rem;
}

.section-header {
    margin-bottom: 2rem;
}

/* ブックマークカード */
.bookmark-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    height: 100%;
}

.bookmark-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
}

.bookmark-image {
    height: 200px;
    overflow: hidden;
    position: relative;
}

.bookmark-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.bookmark-image-placeholder {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.bookmark-image-placeholder i {
    font-size: 3rem;
    color: #6c757d;
}

.bookmark-content {
    padding: 1.5rem;
}

.bookmark-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.bookmark-company {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

/* テーブル改善 */
.table th {
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    color: #495057;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

/* レスポンシブ調整 */
@media (max-width: 768px) {
    .welcome-hero {
        padding: 3rem 0;
        margin-top: -1rem;
    }
    
    .display-4 {
        font-size: 2rem;
    }
    
    .stats-card {
        padding: 1.5rem;
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .feature-card {
        padding: 1.5rem;
    }
    
    .section-title {
        font-size: 1.5rem;
    }
}

@media (max-width: 576px) {
    .welcome-hero {
        padding: 2rem 0;
    }
    
    .stats-card {
        padding: 1rem;
    }
    
    .feature-card {
        padding: 1rem;
    }
}
</style>
@endsection