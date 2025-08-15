@extends('layouts.app')

@section('title', $job->title . ' - 求人詳細')

@section('content')
<div class="container">
    <!-- パンくずナビ -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">ホーム</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('jobs.index') }}">求人一覧</a>
            </li>
            <li class="breadcrumb-item active">{{ Str::limit($job->title, 30) }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- メインコンテンツ -->
        <div class="col-lg-8">
            <!-- 求人情報カード -->
            <div class="card shadow-sm mb-4">
                <!-- 求人画像 -->
                @if($job->image_url)
                    <img src="{{ $job->image_url }}" 
                         class="card-img-top" 
                         alt="{{ $job->title }}"
                         style="height: 300px; object-fit: cover;">
                @endif

                <div class="card-body">
                    <!-- ヘッダー情報 -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <!-- バッジ -->
                            <div class="mb-2">
                                @if($jobStats['is_new'] ?? false)
                                    <span class="badge badge-new me-2">NEW</span>
                                @endif
                                @if(($jobStats['application_count'] ?? 0) >= 10)
                                    <span class="badge badge-popular">人気</span>
                                @endif
                            </div>
                            
                            <!-- 企業名 -->
                            <p class="text-muted mb-1">
                                <i class="fas fa-building me-2"></i>{{ $job->company->company_name }}
                            </p>
                            
                            <!-- 求人タイトル -->
                            <h1 class="h2 mb-0">{{ $job->title }}</h1>
                        </div>

                        <!-- アクションボタン -->
                        <div class="text-end">
                            @auth('job_seeker')
                                <!-- ブックマークボタン -->
                                <div class="mb-2">
                                    @if($isBookmarked ?? false)
                                        <a href="{{ route('bookmarks.removeConfirm', $job->id) }}" 
                                           class="btn btn-warning">
                                            <i class="fas fa-bookmark me-1"></i>ブックマーク済み
                                        </a>
                                    @else
                                        <a href="{{ route('bookmarks.confirm', $job->id) }}" 
                                           class="btn btn-outline-warning">
                                            <i class="far fa-bookmark me-1"></i>ブックマーク
                                        </a>
                                    @endif
                                </div>

                                <!-- 応募ボタン -->
                                @if($isApplied ?? false)
                                    <button class="btn btn-secondary" disabled>
                                        <i class="fas fa-check me-1"></i>応募済み
                                    </button>
                                @else
                                    <a href="{{ route('applications.create', $job->id) }}" 
                                       class="btn btn-success btn-lg">
                                        <i class="fas fa-paper-plane me-1"></i>応募する
                                    </a>
                                @endif
                            @else
                                <div class="text-center">
                                    <p class="text-muted mb-2">応募するにはログインが必要です</p>
                                    <a href="{{ route('job_seeker.login.form') }}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-1"></i>ログイン
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>

                    <!-- 基本情報 -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">募集要項</h5>
                            <dl class="row">
                                <dt class="col-sm-5">勤務地</dt>
                                <dd class="col-sm-7">
                                    <i class="fas fa-map-marker-alt me-1 text-muted"></i>{{ $job->location }}
                                </dd>
                                
                                <dt class="col-sm-5">雇用形態</dt>
                                <dd class="col-sm-7">
                                    <i class="fas fa-briefcase me-1 text-muted"></i>{{ $job->employment_type }}
                                </dd>
                                
                                <dt class="col-sm-5">給与</dt>
                                <dd class="col-sm-7">
                                    <i class="fas fa-yen-sign me-1 text-success"></i>
                                    <span class="text-success fw-bold">{{ $job->salary_range }}</span>
                                </dd>
                                
                                <dt class="col-sm-5">投稿日</dt>
                                <dd class="col-sm-7">
                                    <i class="fas fa-calendar me-1 text-muted"></i>{{ $job->created_at->format('Y年m月d日') }}
                                </dd>
                            </dl>
                        </div>

                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">統計情報</h5>
                            <dl class="row">
                                @if(($jobStats['application_count'] ?? 0) > 0)
                                    <dt class="col-sm-5">応募数</dt>
                                    <dd class="col-sm-7">
                                        <i class="fas fa-users me-1 text-muted"></i>{{ $jobStats['application_count'] }}件
                                    </dd>
                                @endif
                                
                                @if(($jobStats['bookmark_count'] ?? 0) > 0)
                                    <dt class="col-sm-5">ブックマーク</dt>
                                    <dd class="col-sm-7">
                                        <i class="fas fa-bookmark me-1 text-muted"></i>{{ $jobStats['bookmark_count'] }}件
                                    </dd>
                                @endif
                                
                                <dt class="col-sm-5">最終更新</dt>
                                <dd class="col-sm-7">
                                    <i class="fas fa-sync me-1 text-muted"></i>{{ $job->updated_at->format('Y年m月d日') }}
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <!-- 求人内容 -->
                    <div class="mb-4">
                        <h5 class="border-bottom pb-2 mb-3">仕事内容</h5>
                        <div class="job-description">
                            {!! nl2br(e($job->description)) !!}
                        </div>
                    </div>

                    <!-- 応募ボタン（下部） -->
                    @auth('job_seeker')
                        @if(!($isApplied ?? false))
                            <div class="text-center">
                                <a href="{{ route('applications.create', $job->id) }}" 
                                   class="btn btn-success btn-lg px-5">
                                    <i class="fas fa-paper-plane me-2"></i>この求人に応募する
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- 企業情報カード -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>企業情報
                    </h5>
                </div>
                <div class="card-body">
                    <h6>{{ $job->company->company_name }}</h6>
                    <p class="text-muted mb-3">
                        <i class="fas fa-user me-1"></i>担当者: {{ $job->company->contact_name }}
                    </p>
                    
                    <!-- 企業の他の求人へのリンク -->
                    <a href="{{ route('jobs.index', ['keyword' => $job->company->company_name]) }}" 
                       class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-search me-1"></i>この企業の他の求人を見る
                    </a>
                </div>
            </div>
        </div>

        <!-- サイドバー -->
        <div class="col-lg-4">
            <!-- クイックアクション -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-tools me-2"></i>アクション
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @auth('job_seeker')
                            @if(!($isApplied ?? false))
                                <a href="{{ route('applications.create', $job->id) }}" 
                                   class="btn btn-success">
                                    <i class="fas fa-paper-plane me-1"></i>応募する
                                </a>
                            @endif
                            
                            @if($isBookmarked ?? false)
                                <a href="{{ route('bookmarks.removeConfirm', $job->id) }}" 
                                   class="btn btn-warning">
                                    <i class="fas fa-bookmark me-1"></i>ブックマーク削除
                                </a>
                            @else
                                <a href="{{ route('bookmarks.confirm', $job->id) }}" 
                                   class="btn btn-outline-warning">
                                    <i class="far fa-bookmark me-1"></i>ブックマーク追加
                                </a>
                            @endif
                        @else
                            <a href="{{ route('job_seeker.login.form') }}" 
                               class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-1"></i>ログインして応募
                            </a>
                            <a href="{{ route('job_seeker.register.form') }}" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-user-plus me-1"></i>新規登録
                            </a>
                        @endauth
                        
                        <a href="{{ route('jobs.index') }}" 
                           class="btn btn-outline-secondary">
                            <i class="fas fa-list me-1"></i>求人一覧に戻る
                        </a>
                    </div>
                </div>
            </div>

            <!-- 検索フィルター -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2"></i>類似条件で検索
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('jobs.index', ['location' => $job->location]) }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-map-marker-alt me-1"></i>{{ $job->location }}の求人
                        </a>
                        <a href="{{ route('jobs.index', ['employment_type' => $job->employment_type]) }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-briefcase me-1"></i>{{ $job->employment_type }}の求人
                        </a>
                        <a href="{{ route('jobs.index', ['salary_range' => $job->salary_range]) }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-yen-sign me-1"></i>{{ $job->salary_range }}の求人
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 関連求人 -->
    @if(isset($relatedJobs) && $relatedJobs->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h4 class="mb-4">
                <i class="fas fa-building me-2"></i>{{ $job->company->company_name }}の他の求人
            </h4>
            
            <div class="row">
                @foreach($relatedJobs as $relatedJob)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm job-card">
                            @if($relatedJob->image_url)
                                <img src="{{ $relatedJob->image_url }}" 
                                     class="card-img-top" 
                                     alt="{{ $relatedJob->title }}"
                                     style="height: 150px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 150px;">
                                    <i class="fas fa-briefcase fa-2x text-muted"></i>
                                </div>
                            @endif

                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title">
                                    <a href="{{ route('jobs.show', $relatedJob->id) }}" 
                                       class="text-decoration-none">
                                        {{ Str::limit($relatedJob->title, 40) }}
                                    </a>
                                </h6>
                                
                                <p class="card-text text-muted small mb-3">
                                    {{ Str::limit($relatedJob->summary ?? strip_tags($relatedJob->description), 60) }}
                                </p>
                                
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $relatedJob->location }}
                                        </small>
                                        <a href="{{ route('jobs.show', $relatedJob->id) }}" 
                                           class="btn btn-primary btn-sm">
                                            詳細
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
.job-description {
    line-height: 1.8;
    color: #333;
}

.job-description p {
    margin-bottom: 1rem;
}

.badge-new {
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
}

.badge-popular {
    background: linear-gradient(45deg, #ffc107, #fd7e14);
    color: #212529;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
}

.job-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.job-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .btn-lg {
        font-size: 1rem;
        padding: 0.75rem 1.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ブックマーク機能のAjax処理（将来の拡張用）
    const bookmarkBtns = document.querySelectorAll('.bookmark-ajax-btn');
    
    bookmarkBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const jobId = this.dataset.jobId;
            const isBookmarked = this.classList.contains('btn-warning');
            const url = isBookmarked 
                ? `/job_seeker/bookmarks/ajax/remove/${jobId}`
                : `/job_seeker/bookmarks/ajax/add/${jobId}`;
            const method = isBookmarked ? 'DELETE' : 'POST';
            
            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // ボタンの状態を切り替え
                    if (isBookmarked) {
                        this.classList.remove('btn-warning');
                        this.classList.add('btn-outline-warning');
                        this.innerHTML = '<i class="far fa-bookmark me-1"></i>ブックマーク';
                    } else {
                        this.classList.remove('btn-outline-warning');
                        this.classList.add('btn-warning');
                        this.innerHTML = '<i class="fas fa-bookmark me-1"></i>ブックマーク済み';
                    }
                    
                    // 成功メッセージを表示
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'エラーが発生しました', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('エラーが発生しました', 'error');
            });
        });
    });
    
    // トースト表示関数
    function showToast(message, type = 'info') {
        // 簡易的なトースト表示（Bootstrap Toastを使用する場合は適宜修正）
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 'alert-info';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', alertHtml);
        
        // 5秒後に自動削除
        setTimeout(() => {
            const alert = document.querySelector('.alert.position-fixed:last-of-type');
            if (alert) {
                const alertInstance = new bootstrap.Alert(alert);
                alertInstance.close();
            }
        }, 5000);
    }
});
</script>
@endpush
@endsection