@extends('layouts.app')

@section('title', '応募履歴 - 求人応募ポータル')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- ヘッダー部分 -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary">
                    <i class="fas fa-file-alt me-2"></i>応募履歴
                    <small class="text-muted fs-6">（{{ $applications->count() }}件の応募）</small>
                </h2>
                <div class="btn-group" role="group">
                    <a href="{{ route('job_seeker.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>マイページに戻る
                    </a>
                    <a href="{{ route('jobs.index') }}" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>求人を探す
                    </a>
                </div>
            </div>

            <!-- 統計情報 -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-paper-plane fa-2x mb-2"></i>
                            <h5 class="card-title">応募合計</h5>
                            <h2 class="mb-0">{{ $applications->count() }}</h2>
                            <small>件</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-secondary text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h5 class="card-title">応募済</h5>
                            <h2 class="mb-0">{{ $applications->where('status', 0)->count() }}</h2>
                            <small>件</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-spinner fa-2x mb-2"></i>
                            <h5 class="card-title">選考中</h5>
                            <h2 class="mb-0">{{ $applications->where('status', 1)->count() }}</h2>
                            <small>件</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h5 class="card-title">結果通知済</h5>
                            <h2 class="mb-0">{{ $applications->where('status', 2)->count() }}</h2>
                            <small>件</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 通過率表示 -->
            @if($applications->count() > 0)
                <div class="row mb-4">
                    <div class="col-md-12">
                        @php
                            $passedCount = $applications->whereIn('status', [1, 2])->count();
                            $passRate = $applications->count() > 0 ? round(($passedCount / $applications->count()) * 100, 1) : 0;
                        @endphp
                        <div class="card border-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-info mb-1">
                                            <i class="fas fa-chart-line me-2"></i>通過率
                                        </h6>
                                        <span class="text-muted">書類選考・面接などの通過状況</span>
                                    </div>
                                    <div class="text-end">
                                        <h4 class="text-info mb-0">{{ $passRate }}%</h4>
                                        <small class="text-muted">{{ $passedCount }}/{{ $applications->count() }}件通過</small>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $passRate }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 応募履歴一覧 -->
            @if($applications->count() > 0)
                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>応募履歴一覧
                        </h5>
                        <small class="text-muted">最新の応募から表示しています</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 140px;">
                                            <i class="fas fa-calendar-alt me-1"></i>応募日時
                                        </th>
                                        <th style="width: 200px;">
                                            <i class="fas fa-building me-1"></i>企業名
                                        </th>
                                        <th>
                                            <i class="fas fa-briefcase me-1"></i>求人タイトル
                                        </th>
                                        <th style="width: 150px;">
                                            <i class="fas fa-map-marker-alt me-1"></i>勤務地
                                        </th>
                                        <th style="width: 120px;">
                                            <i class="fas fa-flag me-1"></i>ステータス
                                        </th>
                                        <th style="width: 120px;">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications->sortByDesc('applied_at') as $application)
                                        <tr class="align-middle">
                                            <td>
                                                <small class="text-muted">
                                                    <div>{{ $application->applied_at->format('Y/m/d') }}</div>
                                                    <div>{{ $application->applied_at->format('H:i') }}</div>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                        <i class="fas fa-building text-white small"></i>
                                                    </div>
                                                    <div>
                                                        <strong class="d-block">{{ $application->job->company->company_name }}</strong>
                                                        <small class="text-muted">{{ $application->job->company->contact_name ?? '担当者未設定' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('jobs.show', $application->job) }}" 
                                                   class="text-primary text-decoration-none fw-bold">
                                                    {{ $application->job->title }}
                                                </a>
                                                <div class="small text-muted mt-1">
                                                    <i class="fas fa-yen-sign me-1"></i>{{ $application->job->salary_range }}
                                                    <span class="mx-2">|</span>
                                                    <i class="fas fa-user-tie me-1"></i>{{ $application->job->employment_type }}
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ $application->job->location }}
                                                </small>
                                            </td>
                                            <td>
                                                @php
                                                    $statusConfig = [
                                                        0 => ['class' => 'bg-secondary', 'icon' => 'clock', 'text' => '応募済'],
                                                        1 => ['class' => 'bg-warning', 'icon' => 'spinner', 'text' => '選考中'],
                                                        2 => ['class' => 'bg-success', 'icon' => 'check', 'text' => '結果通知済']
                                                    ];
                                                    $status = $statusConfig[$application->status] ?? $statusConfig[0];
                                                @endphp
                                                <span class="badge {{ $status['class'] }} d-flex align-items-center">
                                                    <i class="fas fa-{{ $status['icon'] }} me-1"></i>
                                                    {{ $status['text'] }}
                                                </span>
                                                @if($application->status > 0)
                                                    <small class="d-block text-muted mt-1">
                                                        {{ $application->updated_at->format('m/d更新') }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group-vertical d-grid gap-1" role="group">
                                                    <a href="{{ route('jobs.show', $application->job) }}" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="求人詳細を見る">
                                                        <i class="fas fa-eye me-1"></i>詳細
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-info"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#applicationModal{{ $application->id }}"
                                                            title="応募内容を見る">
                                                        <i class="fas fa-file-text me-1"></i>応募内容
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- 応募内容詳細モーダル -->
                                        <div class="modal fade" id="applicationModal{{ $application->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-file-text me-2"></i>応募内容詳細
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h6><i class="fas fa-briefcase me-2"></i>求人情報</h6>
                                                                <div class="bg-light p-3 rounded">
                                                                    <p class="mb-1"><strong>求人タイトル:</strong> {{ $application->job->title }}</p>
                                                                    <p class="mb-1"><strong>企業名:</strong> {{ $application->job->company->company_name }}</p>
                                                                    <p class="mb-1"><strong>勤務地:</strong> {{ $application->job->location }}</p>
                                                                    <p class="mb-0"><strong>応募日時:</strong> {{ $application->applied_at->format('Y年m月d日 H:i') }}</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6><i class="fas fa-user me-2"></i>連絡先情報</h6>
                                                                <div class="bg-light p-3 rounded">
                                                                    <p class="mb-1"><strong>メールアドレス:</strong> {{ $application->email }}</p>
                                                                    <p class="mb-1"><strong>電話番号:</strong> {{ $application->phone }}</p>
                                                                    <p class="mb-0">
                                                                        <strong>ステータス:</strong>
                                                                        <span class="badge {{ $status['class'] }} ms-1">{{ $status['text'] }}</span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <h6><i class="fas fa-heart me-2"></i>志望動機</h6>
                                                        <div class="border p-3 rounded bg-light" style="max-height: 200px; overflow-y: auto;">
                                                            {{ $application->motivation }}
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            <i class="fas fa-times me-1"></i>閉じる
                                                        </button>
                                                        <a href="{{ route('jobs.show', $application->job) }}" class="btn btn-primary">
                                                            <i class="fas fa-external-link-alt me-1"></i>求人詳細を見る
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ページネーション -->
                @if($applications instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="d-flex justify-content-center mt-4">
                        {{ $applications->links() }}
                    </div>
                @endif

            @else
                <!-- 応募履歴がない場合 -->
                <div class="card border-0">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-clipboard-list fa-4x text-muted"></i>
                        </div>
                        <h4 class="text-muted mb-3">まだ応募履歴がありません</h4>
                        <p class="text-muted mb-4">
                            気になる求人に応募すると、こちらに履歴が表示されます。<br>
                            まずは求人を探してみましょう！
                        </p>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="{{ route('jobs.index') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-search me-2"></i>求人を探す
                            </a>
                            <a href="{{ route('job_seeker.dashboard') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-user me-2"></i>マイページ
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.card:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.table tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

.progress {
    background-color: rgba(23,162,184,0.1);
}

.btn-group-vertical .btn {
    font-size: 0.875rem;
}

.modal-body {
    line-height: 1.6;
}

@media (max-width: 768px) {
    .btn-group-vertical {
        width: 100%;
    }
    
    .btn-group-vertical .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>
@endsection