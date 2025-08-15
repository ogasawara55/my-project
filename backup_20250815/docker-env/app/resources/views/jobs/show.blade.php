@extends('layouts.app')

@section('title', $job->title . ' - 求人詳細')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <!-- パンくずリスト -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('jobs.index') }}">求人一覧</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $job->title }}</li>
                </ol>
            </nav>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- 求人詳細 -->
                <div class="col-md-8">
                    <div class="card shadow">
                        @if($job->image_url)
                            <img src="{{ $job->image_url }}" class="card-img-top" style="height: 300px; object-fit: cover;" alt="求人画像">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                                <i class="fas fa-building fa-5x text-muted"></i>
                            </div>
                        @endif
                        
                        <div class="card-body">
                            <h1 class="card-title h3 text-primary">{{ $job->title }}</h1>
                            <h2 class="h5 text-muted mb-4">
                                <i class="fas fa-building me-2"></i>{{ $job->company->company_name }}
                            </h2>
                            
                            <!-- 求人基本情報 -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-map-marker-alt fa-lg me-3 text-primary"></i>
                                        <div>
                                            <strong>勤務地</strong><br>
                                            <span class="text-muted">{{ $job->location }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-yen-sign fa-lg me-3 text-success"></i>
                                        <div>
                                            <strong>給与</strong><br>
                                            <span class="text-muted">{{ $job->salary_range }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-briefcase fa-lg me-3 text-info"></i>
                                        <div>
                                            <strong>雇用形態</strong><br>
                                            <span class="text-muted">{{ $job->employment_type }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-calendar fa-lg me-3 text-warning"></i>
                                        <div>
                                            <strong>投稿日</strong><br>
                                            <span class="text-muted">{{ $job->created_at->format('Y年m月d日') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 仕事内容 -->
                            @if($job->description)
                                <div class="mb-4">
                                    <h3 class="h5 border-bottom pb-2 mb-3">
                                        <i class="fas fa-file-alt me-2 text-secondary"></i>仕事内容
                                    </h3>
                                    <div class="bg-light p-3 rounded">
                                        {!! nl2br(e($job->description)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- サイドバー -->
                <div class="col-md-4">
                    <!-- アクションカード -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-hand-paper me-2"></i>この求人に対するアクション
                            </h5>
                        </div>
                        <div class="card-body">
                            @auth('job_seeker')
                                @php
                                    $isApplied = isset($isApplied) ? $isApplied : false;
                                    $isBookmarked = isset($isBookmarked) ? $isBookmarked : false;
                                @endphp
                                
                                <!-- 応募ボタン -->
                                <div class="mb-3">
                                    @if($isApplied)
                                        <button class="btn btn-success btn-lg w-100" disabled>
                                            <i class="fas fa-check me-2"></i>応募済み
                                        </button>
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-info-circle me-1"></i>この求人には既に応募済みです
                                        </small>
                                    @else
                                        <a href="{{ route('job_seeker.jobs.apply', $job->id) }}" class="btn btn-success btn-lg w-100">
                                            <i class="fas fa-paper-plane me-2"></i>この求人に応募する
                                        </a>
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-lightbulb me-1"></i>応募には志望動機の入力が必要です
                                        </small>
                                    @endif
                                </div>
                                
                                <!-- ブックマークボタン（Ajax版） -->
                                <div class="mb-3">
                                    <button type="button" 
                                            class="btn bookmark-btn btn-lg w-100 {{ $isBookmarked ? 'btn-warning' : 'btn-outline-warning' }}" 
                                            data-job-id="{{ $job->id }}"
                                            data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}">
                                        <i class="bookmark-icon {{ $isBookmarked ? 'fas' : 'far' }} fa-bookmark me-2"></i>
                                        <span class="bookmark-text">{{ $isBookmarked ? 'ブックマーク済み' : 'ブックマークする' }}</span>
                                    </button>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-lightbulb me-1"></i>
                                        <span class="bookmark-help">{{ $isBookmarked ? 'クリックでブックマークから削除' : '後で確認したい求人をブックマーク' }}</span>
                                    </small>
                                </div>
                                
                                <!-- マイページリンク -->
                                <div>
                                    <a href="{{ route('job_seeker.dashboard') }}" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-user me-2"></i>マイページに戻る
                                    </a>
                                </div>
                                
                            @else
                                <!-- 未ログインユーザー -->
                                <div class="text-center mb-3">
                                    <p class="text-muted">この求人に応募・ブックマークするにはログインが必要です</p>
                                </div>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('job_seeker.login.form') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-sign-in-alt me-2"></i>ログイン
                                    </a>
                                    <a href="{{ route('job_seeker.register.form') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-user-plus me-2"></i>新規登録
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>
                    
                    <!-- 企業情報カード -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-building me-2"></i>企業情報
                            </h5>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">{{ $job->company->company_name }}</h6>
                            <p class="card-text">
                                <strong>担当者:</strong> {{ $job->company->contact_name }}<br>
                                <strong>メール:</strong> {{ $job->company->email }}
                            </p>
                            
                            @if(isset($companyJobsCount))
                                <div class="text-center">
                                    <small class="text-muted">
                                        この企業の他の求人: {{ $companyJobsCount }}件
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- 統計情報 -->
                    <div class="card shadow">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>この求人の統計
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h4 class="text-primary">{{ $applicationCount ?? 0 }}</h4>
                                    <small class="text-muted">応募者数</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-warning">{{ $bookmarkCount ?? 0 }}</h4>
                                    <small class="text-muted">ブックマーク数</small>
                                </div>
                            </div>
                            
                            @if($job->created_at->diffInDays(now()) <= 7)
                                <div class="text-center mt-3">
                                    <span class="badge bg-success">
                                        <i class="fas fa-star me-1"></i>新着求人
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 関連求人 -->
            @if(isset($relatedJobs) && $relatedJobs->count() > 0)
                <div class="row mt-5">
                    <div class="col-md-12">
                        <h3 class="h4 mb-4">
                            <i class="fas fa-link me-2 text-primary"></i>{{ $job->company->company_name }}の他の求人
                        </h3>
                        <div class="row">
                            @foreach($relatedJobs as $relatedJob)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        @if($relatedJob->image_url)
                                            <img src="{{ $relatedJob->image_url }}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="求人画像">
                                        @else
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                                <i class="fas fa-briefcase fa-2x text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="card-body">
                                            <h6 class="card-title">{{ Str::limit($relatedJob->title, 40) }}</h6>
                                            <p class="card-text small">
                                                <i class="fas fa-map-marker-alt me-1"></i>{{ $relatedJob->location }}<br>
                                                <i class="fas fa-yen-sign me-1"></i>{{ $relatedJob->salary_range }}
                                            </p>
                                            <a href="{{ route('jobs.show', $relatedJob->id) }}" class="btn btn-sm btn-outline-primary">詳細を見る</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 10px;
}

.card-img-top {
    border-radius: 10px 10px 0 0;
}

.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.bg-light {
    background-color: #f8f9fa !important;
}

/* ブックマークボタンのアニメーション */
.bookmark-btn {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.bookmark-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.bookmark-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.bookmark-btn.processing {
    pointer-events: none;
}

.bookmark-icon {
    transition: all 0.3s ease;
}

.bookmark-text {
    transition: all 0.3s ease;
}

@media (max-width: 768px) {
    .container {
        padding: 0 15px;
    }
    
    .card-body {
        padding: 1rem;
    }
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    console.log('ブックマーク機能 初期化開始');
    
    // CSRFトークンを取得
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    if (!csrfToken) {
        console.error('CSRFトークンが見つかりません');
        return;
    }
    console.log('CSRFトークン確認済み');

    // ブックマークボタンの処理
    $('.bookmark-btn').on('click', function() {
        console.log('ブックマークボタンがクリックされました');
        
        const $button = $(this);
        const jobId = $button.data('job-id');
        const isBookmarked = $button.data('bookmarked') === true || $button.data('bookmarked') === 'true';
        
        console.log('求人ID:', jobId);
        console.log('現在のブックマーク状態:', isBookmarked);
        
        // ボタンを無効化（連続クリック防止）
        $button.prop('disabled', true).addClass('processing');
        
        // 元の状態を保存
        const $icon = $button.find('.bookmark-icon');
        const $text = $button.find('.bookmark-text');
        const $help = $('.bookmark-help');
        const originalIconClass = $icon.attr('class');
        const originalText = $text.text();
        const originalHelpText = $help.text();
        
        // ローディング表示
        $text.text('処理中...');
        $icon.attr('class', 'bookmark-icon fas fa-spinner fa-spin me-2');

        // Ajax リクエスト設定
        const url = isBookmarked 
            ? `/job_seeker/api/bookmarks/${jobId}/remove`
            : `/job_seeker/api/bookmarks/${jobId}/add`;
        
        const method = isBookmarked ? 'DELETE' : 'POST';
        
        console.log('リクエストURL:', url);
        console.log('リクエストメソッド:', method);

        $.ajax({
            url: url,
            type: method,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            success: function(data) {
                console.log('Ajax成功:', data);
                
                if (data.success) {
                    // 成功時の処理
                    const newIsBookmarked = !isBookmarked;
                    console.log('新しいブックマーク状態:', newIsBookmarked);
                    
                    // ボタンの状態を更新
                    $button.data('bookmarked', newIsBookmarked);
                    
                    if (newIsBookmarked) {
                        // ブックマーク追加後
                        $button.removeClass('btn-outline-warning').addClass('btn-warning');
                        $icon.attr('class', 'bookmark-icon fas fa-bookmark me-2');
                        $text.text('ブックマーク済み');
                        $help.text('クリックでブックマークから削除');
                        console.log('ブックマーク追加UI更新完了');
                    } else {
                        // ブックマーク削除後
                        $button.removeClass('btn-warning').addClass('btn-outline-warning');
                        $icon.attr('class', 'bookmark-icon far fa-bookmark me-2');
                        $text.text('ブックマークする');
                        $help.text('後で確認したい求人をブックマーク');
                        console.log('ブックマーク削除UI更新完了');
                    }

                    // 成功メッセージを表示
                    showMessage(data.message, 'success');
                    
                } else {
                    console.error('Ajax処理失敗:', data.message);
                    // エラーメッセージを表示
                    showMessage(data.message || 'エラーが発生しました', 'error');
                    // 元の状態に戻す
                    restoreButton();
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajax エラー:', xhr, status, error);
                console.error('Response:', xhr.responseJSON);
                
                let message = '通信エラーが発生しました';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                showMessage(message, 'error');
                // 元の状態に戻す
                restoreButton();
            },
            complete: function() {
                // ボタンを有効化
                $button.prop('disabled', false).removeClass('processing');
                console.log('Ajax処理完了、ボタン有効化');
            }
        });

        // ボタンを元の状態に戻す関数
        function restoreButton() {
            console.log('ボタンを元の状態に戻します');
            $icon.attr('class', originalIconClass);
            $text.text(originalText);
            $help.text(originalHelpText);
        }
    });

    // メッセージ表示関数
    function showMessage(message, type) {
        console.log('メッセージ表示:', message, type);
        
        // 既存のアラートがあれば削除
        $('.ajax-alert').remove();

        // 新しいアラートを作成
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show ajax-alert" role="alert">
                <i class="fas ${iconClass} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // パンくずリストの下に表示
        $('nav[aria-label="breadcrumb"]').after(alertHtml);
        
        // 3秒後に自動で削除
        setTimeout(function() {
            $('.ajax-alert').fadeOut();
        }, 3000);
    }
    
    console.log('ブックマーク機能 初期化完了');
});
</script>
@endpush
@endsection