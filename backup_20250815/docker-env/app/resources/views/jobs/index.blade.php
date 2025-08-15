@extends('layouts.app')

@section('title', '求人一覧')

@push('styles')
<style>
/* 求人一覧専用スタイル */
.job-card {
    transition: all 0.3s ease;
}

.job-card:hover {
    transform: translateY(-5px);
}

.bookmark-btn {
    transition: all 0.3s ease;
    position: relative;
}

.bookmark-btn:disabled {
    opacity: 0.6;
    pointer-events: none;
}

.bookmark-btn.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 16px;
    height: 16px;
    margin: -8px 0 0 -8px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.search-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.search-card .form-control,
.search-card .form-select {
    background-color: rgba(255, 255, 255, 0.9);
    border: none;
}

.search-card .form-label {
    color: white;
    font-weight: 500;
}

.view-toggle .btn-check:checked + .btn {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

/* リスト表示スタイル */
.list-view .job-card {
    margin-bottom: 1rem;
}

.list-view .job-card .card {
    flex-direction: row;
    align-items: center;
}

.list-view .job-card .card-img-top {
    width: 200px;
    height: 150px;
    border-radius: 10px 0 0 10px;
    object-fit: cover;
}

.list-view .job-card .card-body {
    flex: 1;
}

@media (max-width: 768px) {
    .list-view .job-card .card {
        flex-direction: column;
    }
    
    .list-view .job-card .card-img-top {
        width: 100%;
        border-radius: 10px 10px 0 0;
    }
}

.job-stats {
    font-size: 0.85rem;
    color: #6c757d;
}

.new-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
}

.card-img-container {
    position: relative;
    overflow: hidden;
}

.fade-in {
    animation: fadeIn 0.6s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endpush

@section('content')
<div class="container fade-in">
    <div class="row">
        <div class="col-md-12">
            <!-- ページヘッダー -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-briefcase text-primary me-2"></i>求人一覧</h2>
                <div>
                    @auth('job_seeker')
                        <a href="{{ route('job_seeker.dashboard') }}" class="btn btn-outline-primary">
                            <i class="fas fa-user me-2"></i>マイページ
                        </a>
                    @else
                        <a href="{{ route('job_seeker.login.form') }}" class="btn btn-outline-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>ログイン
                        </a>
                    @endauth
                </div>
            </div>

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

            <!-- 検索フォーム -->
            <div class="card mb-4 search-card">
                <div class="card-header border-0">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-search me-2"></i>求人検索
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('jobs.index') }}" id="searchForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="search" class="form-label">キーワード</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="求人タイトル、企業名、仕事内容">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="location" class="form-label">勤務地</label>
                                <select class="form-select" id="location" name="location">
                                    <option value="">すべて</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                            {{ $location }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="employment_type" class="form-label">雇用形態</label>
                                <select class="form-select" id="employment_type" name="employment_type">
                                    <option value="">すべて</option>
                                    @foreach($employmentTypes as $type)
                                        <option value="{{ $type }}" {{ request('employment_type') == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="salary_range" class="form-label">給与</label>
                                <select class="form-select" id="salary_range" name="salary_range">
                                    <option value="">すべて</option>
                                    @foreach($salaryRanges as $range)
                                        <option value="{{ $range }}" {{ request('salary_range') == $range ? 'selected' : '' }}>
                                            {{ $range }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-light me-2">
                                    <i class="fas fa-search me-2"></i>検索
                                </button>
                                <a href="{{ route('jobs.index') }}" class="btn btn-outline-light">
                                    <i class="fas fa-undo me-2"></i>リセット
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 検索結果 -->
            @if($jobs->count() > 0)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="fas fa-list-alt me-2 text-primary"></i>
                        検索結果: <span class="text-primary">{{ number_format($jobs->total()) }}</span>件
                    </h5>
                    <div class="btn-group view-toggle" role="group">
                        <input type="radio" class="btn-check" name="view" id="grid-view" checked>
                        <label class="btn btn-outline-primary" for="grid-view" title="グリッド表示">
                            <i class="fas fa-th"></i>
                        </label>
                        <input type="radio" class="btn-check" name="view" id="list-view">
                        <label class="btn btn-outline-primary" for="list-view" title="リスト表示">
                            <i class="fas fa-list"></i>
                        </label>
                    </div>
                </div>

                <!-- 求人カード表示 -->
                <div class="row" id="jobs-container">
                    @foreach($jobs as $job)
                        <div class="col-md-6 col-lg-4 mb-4 job-card">
                            <div class="card h-100 shadow-sm">
                                <div class="card-img-container">
                                    @if($job->image_url)
                                        <img src="{{ $job->image_url }}" 
                                             class="card-img-top" 
                                             style="height: 200px; object-fit: cover;" 
                                             alt="求人画像"
                                             loading="lazy">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 200px;">
                                            <i class="fas fa-building fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    @if($job->created_at->diffInDays(now()) <= 7)
                                        <span class="badge bg-success new-badge">
                                            <i class="fas fa-star me-1"></i>NEW
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title mb-2">{{ $job->title }}</h5>
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-building me-1"></i>{{ $job->company->company_name }}
                                    </h6>
                                    
                                    <div class="mb-3">
                                        <div class="job-stats">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                                <span>{{ $job->location }}</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="fas fa-yen-sign me-2 text-success"></i>
                                                <span>{{ $job->salary_range }}</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="fas fa-briefcase me-2 text-info"></i>
                                                <span>{{ $job->employment_type }}</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-calendar me-2 text-warning"></i>
                                                <span>{{ $job->created_at->format('Y/m/d') }}</span>
                                            </div>
                                        </div>
                                        
                                        @if($job->description)
                                            <p class="card-text text-muted small mt-2">
                                                {{ Str::limit(strip_tags($job->description), 80) }}
                                            </p>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex gap-2 mb-2">
                                            <a href="{{ route('jobs.show', $job->id) }}" 
                                               class="btn btn-primary flex-fill">
                                                <i class="fas fa-eye me-1"></i>詳細を見る
                                            </a>
                                        </div>
                                        
                                        @auth('job_seeker')
    <div class="d-flex gap-2">
        @php
            $isApplied = isset($userApplications) && in_array($job->id, $userApplications);
            $isBookmarked = isset($userBookmarks) && in_array($job->id, $userBookmarks);
        @endphp
        
        @if($isApplied)
            <button class="btn btn-success flex-fill" disabled>
                <i class="fas fa-check me-1"></i>応募済み
            </button>
        @else
            <a href="{{ route('job_seeker.jobs.apply', $job->id) }}" 
               class="btn btn-outline-success flex-fill">
                <i class="fas fa-paper-plane me-1"></i>応募する
            </a>
        @endif
        
        <button type="button" 
                class="btn bookmark-btn {{ $isBookmarked ? 'btn-warning' : 'btn-outline-warning' }}" 
                data-job-id="{{ $job->id }}"
                data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}"
                title="{{ $isBookmarked ? 'ブックマークから削除' : 'ブックマークに追加' }}">
            <i class="{{ $isBookmarked ? 'fas' : 'far' }} fa-bookmark"></i>
        </button>
    </div>
@else
    <div class="d-flex gap-2">
        <a href="{{ route('job_seeker.login.form') }}" 
           class="btn btn-outline-success flex-fill">
            <i class="fas fa-sign-in-alt me-1"></i>ログインして応募
        </a>
        <a href="{{ route('job_seeker.login.form') }}" 
           class="btn btn-outline-warning" 
           title="ログインしてブックマーク">
            <i class="far fa-bookmark"></i>
        </a>
    </div>
@endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- ページネーション -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $jobs->appends(request()->query())->links() }}
                </div>

            @else
                <!-- 求人が見つからない場合 -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-search fa-5x text-muted"></i>
                    </div>
                    <h4 class="text-muted mb-3">検索条件に一致する求人が見つかりませんでした</h4>
                    <p class="text-muted mb-4">
                        検索条件を変更してもう一度お試しください。<br>
                        または、すべての求人を確認してみてください。
                    </p>
                    <div>
                        <a href="{{ route('jobs.index') }}" class="btn btn-primary me-2">
                            <i class="fas fa-undo me-2"></i>検索条件をリセット
                        </a>
                        <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>すべての求人を見る
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // ビュー切り替え機能
    $('#grid-view, #list-view').on('change', function() {
        const $container = $('#jobs-container');
        
        if ($('#list-view').is(':checked')) {
            $container.removeClass('row').addClass('list-view');
        } else {
            $container.addClass('row').removeClass('list-view');
        }
    });

    // ブックマーク機能のAjax処理
    $(document).on('click', '.bookmark-btn', function(e) {
        e.preventDefault();
        
        const $btn = $(this);
        const jobId = $btn.data('job-id');
        const isBookmarked = $btn.data('bookmarked') === 'true';
        const $icon = $btn.find('i');
        
        // ボタンを一時的に無効化
        $btn.prop('disabled', true).addClass('loading');
        $icon.css('opacity', '0');
        
        const url = isBookmarked ? 
            `/job_seeker/api/bookmarks/${jobId}/remove` : 
            `/job_seeker/api/bookmarks/${jobId}/add`;
        
        const method = isBookmarked ? 'DELETE' : 'POST';
        
        $.ajax({
            url: url,
            method: method,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(data) {
                if (data.success) {
                    // ブックマーク状態を更新
                    const newBookmarked = !isBookmarked;
                    $btn.data('bookmarked', newBookmarked);
                    
                    // ボタンの見た目を更新
                    if (newBookmarked) {
                        $btn.removeClass('btn-outline-warning').addClass('btn-warning');
                        $btn.attr('title', 'ブックマークから削除');
                        $icon.removeClass('far').addClass('fas');
                    } else {
                        $btn.removeClass('btn-warning').addClass('btn-outline-warning');
                        $btn.attr('title', 'ブックマークに追加');
                        $icon.removeClass('fas').addClass('far');
                    }
                    
                    // 成功アニメーション
                    $btn.css('transform', 'scale(1.1)');
                    setTimeout(() => {
                        $btn.css('transform', 'scale(1)');
                    }, 200);
                    
                    // 成功メッセージ
                    const message = newBookmarked ? 
                        'ブックマークに追加しました' : 
                        'ブックマークから削除しました';
                    showAlert(message, 'success');
                    
                } else {
                    showAlert(data.message || 'エラーが発生しました', 'danger');
                }
            },
            error: function(xhr) {
                console.error('Bookmark error:', xhr);
                
                if (xhr.status === 401) {
                    showAlert('ログインが必要です', 'warning');
                    setTimeout(() => {
                        window.location.href = '/job_seeker/login';
                    }, 2000);
                } else {
                    showAlert('通信エラーが発生しました。もう一度お試しください。', 'danger');
                }
            },
            complete: function() {
                // ローディング状態を解除
                $btn.prop('disabled', false).removeClass('loading');
                $icon.css('opacity', '1');
            }
        });
    });

    // 検索フォームの拡張機能
    $('#searchForm').on('submit', function() {
        showLoading();
    });

    // リアルタイム検索（オプション）
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val();
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                // 検索候補を取得（実装する場合）
                // getSuggestions(query);
            }, 500);
        }
    });

    // カードホバーエフェクト強化
    $('.job-card').hover(
        function() {
            $(this).find('.card').addClass('shadow-lg');
        },
        function() {
            $(this).find('.card').removeClass('shadow-lg');
        }
    );

    // スクロール時のアニメーション
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // 求人カードにアニメーションを適用
    $('.job-card').each(function(index) {
        $(this).css({
            'opacity': '0',
            'transform': 'translateY(20px)',
            'transition': 'opacity 0.6s ease, transform 0.6s ease'
        });
        
        setTimeout(() => {
            observer.observe(this);
        }, index * 100);
    });

    console.log('求人一覧ページが読み込まれました');
});
</script>
@endpush