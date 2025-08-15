{{-- Ajax用求人リスト部分ビュー --}}
@if($jobs->count() > 0)
    @foreach($jobs as $job)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm job-card" data-job-id="{{ $job->id }}">
                <!-- 求人画像 -->
                @if($job->image_url)
                    <img src="{{ $job->image_url }}" 
                         class="card-img-top" 
                         alt="{{ $job->title }}"
                         style="height: 200px; object-fit: cover;">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                         style="height: 200px;">
                        <i class="fas fa-building fa-3x text-muted"></i>
                    </div>
                @endif

                <!-- バッジ（新着・人気など） -->
                @if($job->is_new ?? false)
                    <div class="position-absolute" style="top: 10px; left: 10px; z-index: 10;">
                        <span class="badge badge-new">NEW</span>
                    </div>
                @endif

                @if(($job->application_count ?? 0) >= 10)
                    <div class="position-absolute" style="top: 10px; left: {{ ($job->is_new ?? false) ? '60px' : '10px' }}; z-index: 10;">
                        <span class="badge badge-popular">人気</span>
                    </div>
                @endif

                <div class="card-body d-flex flex-column">
                    <!-- 企業名 -->
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-building me-1"></i>{{ $job->company->company_name }}
                        </small>
                    </div>

                    <!-- 求人タイトル -->
                    <h5 class="card-title mb-2">
                        <a href="{{ route('jobs.show', $job->id) }}" 
                           class="text-decoration-none text-dark">
                            {{ Str::limit($job->title, 50) }}
                        </a>
                    </h5>

                    <!-- 求人詳細（要約） -->
                    <p class="card-text text-muted mb-3">
                        {{ $job->summary ?? Str::limit(strip_tags($job->description), 80) }}
                    </p>

                    <!-- 求人情報 -->
                    <div class="mb-3">
                        <div class="row g-2">
                            <div class="col-12">
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $job->location }}
                                </small>
                            </div>
                            <div class="col-12">
                                <small class="text-muted">
                                    <i class="fas fa-briefcase me-1"></i>{{ $job->employment_type }}
                                </small>
                            </div>
                            <div class="col-12">
                                <small class="text-success fw-bold">
                                    <i class="fas fa-yen-sign me-1"></i>{{ $job->salary_range }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- 統計情報 -->
                    @if(($job->application_count ?? 0) > 0 || ($job->bookmark_count ?? 0) > 0)
                        <div class="mb-3">
                            <div class="row g-2">
                                @if(($job->application_count ?? 0) > 0)
                                    <div class="col-6">
                                        <small class="text-muted">
                                            <i class="fas fa-users me-1"></i>応募 {{ $job->application_count }}件
                                        </small>
                                    </div>
                                @endif
                                @if(($job->bookmark_count ?? 0) > 0)
                                    <div class="col-6">
                                        <small class="text-muted">
                                            <i class="fas fa-bookmark me-1"></i>{{ $job->bookmark_count }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- アクションボタン -->
                    <div class="mt-auto">
                        <div class="d-flex gap-2">
                            <a href="{{ route('jobs.show', $job->id) }}" 
                               class="btn btn-primary btn-sm flex-fill">
                                <i class="fas fa-eye me-1"></i>詳細を見る
                            </a>
                            
                            @auth('job_seeker')
                                <!-- ブックマークボタン -->
                                @php
                                    $isBookmarked = false;
                                    try {
                                        $isBookmarked = $job->isBookmarkedBy(Auth::guard('job_seeker')->id());
                                    } catch (\Exception $e) {
                                        $isBookmarked = false;
                                    }
                                @endphp
                                
                                @if($isBookmarked)
                                    <button class="btn btn-warning btn-sm bookmark-ajax-btn" 
                                            data-job-id="{{ $job->id }}"
                                            data-action="remove"
                                            title="ブックマークから削除">
                                        <i class="fas fa-bookmark"></i>
                                    </button>
                                @else
                                    <button class="btn btn-outline-warning btn-sm bookmark-ajax-btn" 
                                            data-job-id="{{ $job->id }}"
                                            data-action="add"
                                            title="ブックマークに追加">
                                        <i class="far fa-bookmark"></i>
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- 投稿日 -->
                <div class="card-footer bg-transparent">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        投稿日: {{ $job->created_at->format('Y年m月d日') }}
                    </small>
                </div>
            </div>
        </div>
    @endforeach
@else
    <!-- 求人が見つからない場合 -->
    <div class="col-12">
        <div class="text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">求人が見つかりませんでした</h4>
            <p class="text-muted">検索条件を変更してもう一度お試しください。</p>
            <a href="{{ route('jobs.index') }}" class="btn btn-primary">
                <i class="fas fa-list me-1"></i>全ての求人を見る
            </a>
        </div>
    </div>
@endif

{{-- スタイル --}}
<style>
.badge-new {
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
}

.badge-popular {
    background: linear-gradient(45deg, #ffc107, #fd7e14);
    color: #212529;
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
}

.job-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.job-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15) !important;
}

.bookmark-ajax-btn {
    transition: all 0.2s ease-in-out;
}

.bookmark-ajax-btn:hover {
    transform: scale(1.1);
}

.bookmark-ajax-btn.loading {
    pointer-events: none;
    opacity: 0.6;
}

@media (max-width: 768px) {
    .job-card {
        margin-bottom: 1rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .btn-sm {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
}
</style>

{{-- JavaScript（ブックマーク機能） --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ブックマーク機能のAjax処理
    const bookmarkBtns = document.querySelectorAll('.bookmark-ajax-btn');
    
    bookmarkBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (this.classList.contains('loading')) {
                return; // 既に処理中の場合は何もしない
            }
            
            const jobId = this.dataset.jobId;
            const action = this.dataset.action;
            const isAdd = action === 'add';
            
            const url = isAdd 
                ? `/job_seeker/bookmarks/ajax/add/${jobId}`
                : `/job_seeker/bookmarks/ajax/remove/${jobId}`;
            const method = isAdd ? 'POST' : 'DELETE';
            
            // ローディング状態を設定
            this.classList.add('loading');
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
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
                    if (isAdd) {
                        this.classList.remove('btn-outline-warning');
                        this.classList.add('btn-warning');
                        this.dataset.action = 'remove';
                        this.innerHTML = '<i class="fas fa-bookmark"></i>';
                        this.title = 'ブックマークから削除';
                    } else {
                        this.classList.remove('btn-warning');
                        this.classList.add('btn-outline-warning');
                        this.dataset.action = 'add';
                        this.innerHTML = '<i class="far fa-bookmark"></i>';
                        this.title = 'ブックマークに追加';
                    }
                    
                    // 成功メッセージを表示（オプション）
                    showToast(data.message, 'success');
                } else {
                    // エラーの場合は元に戻す
                    this.innerHTML = originalContent;
                    showToast(data.message || 'エラーが発生しました', 'error');
                }
            })
            .catch(error => {
                console.error('Bookmark error:', error);
                // エラーの場合は元に戻す
                this.innerHTML = originalContent;
                showToast('ネットワークエラーが発生しました', 'error');
            })
            .finally(() => {
                // ローディング状態を解除
                this.classList.remove('loading');
            });
        });
    });
    
    // トースト表示関数
    function showToast(message, type = 'info', duration = 3000) {
        // 既存のトーストがあれば削除
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) {
            existingToast.remove();
        }
        
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 'alert-info';
        
        const toastHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed toast-notification" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', toastHtml);
        
        // 指定時間後に自動削除
        setTimeout(() => {
            const toast = document.querySelector('.toast-notification');
            if (toast) {
                const alertInstance = new bootstrap.Alert(toast);
                alertInstance.close();
            }
        }, duration);
    }
});
</script>