@extends('layouts.company')

@section('title', '求人管理')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- ヘッダー部分 -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-2">求人管理</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.dashboard') }}">ダッシュボード</a>
                            </li>
                            <li class="breadcrumb-item active">求人管理</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('company.jobs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> 新規求人投稿
                    </a>
                </div>
            </div>

            <!-- 統計情報 -->
            <div class="row mb-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">投稿求人数</h5>
                                    <h2 class="mb-0">{{ $jobs->count() }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-briefcase fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">総応募数</h5>
                                    <h2 class="mb-0">{{ $totalApplications }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">選考中</h5>
                                    <h2 class="mb-0">{{ $inProgress }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">通過合計</h5>
                                    <h2 class="mb-0">{{ $passedTotal }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 求人一覧 -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> 投稿済み求人一覧
                        @if($jobs->count() > 0)
                            <span class="badge bg-secondary ms-2">{{ $jobs->count() }}件</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($jobs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">求人情報</th>
                                        <th scope="col">応募状況</th>
                                        <th scope="col">投稿日</th>
                                        <th scope="col">ステータス</th>
                                        <th scope="col">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jobs as $job)
                                        <tr>
                                            <td>
                                                <div>
                                                    <h6 class="mb-1">{{ $job->title }}</h6>
                                                    <div class="text-muted small">
                                                        <i class="fas fa-map-marker-alt"></i> {{ $job->location }} | 
                                                        <i class="fas fa-yen-sign"></i> {{ $job->salary_range }} | 
                                                        <i class="fas fa-briefcase"></i> {{ $job->employment_type }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <span class="badge bg-primary">
                                                        応募: {{ $job->applications_count ?? 0 }}件
                                                    </span>
                                                    <span class="badge bg-success">
                                                        通過: {{ $job->passed_count ?? 0 }}件
                                                    </span>
                                                </div>
                                                @if(($job->applications_count ?? 0) > 0)
                                                    <div class="mt-1">
                                                        <small class="text-muted">
                                                            通過率: {{ $job->applications_count > 0 ? round(($job->passed_count ?? 0) / $job->applications_count * 100, 1) : 0 }}%
                                                        </small>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-muted">
                                                    {{ $job->created_at->format('Y/m/d') }}<br>
                                                    <small>{{ $job->created_at->format('H:i') }}</small>
                                                </span>
                                            </td>
                                            <td>
                                                @if($job->applications_count > 0)
                                                    <span class="badge bg-success">応募受付中</span>
                                                @else
                                                    <span class="badge bg-secondary">応募待ち</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('company.jobs.show', $job) }}" 
                                                       class="btn btn-sm btn-info" title="詳細">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('company.jobs.applications', $job) }}" 
                                                       class="btn btn-sm btn-primary" title="応募者一覧">
                                                        <i class="fas fa-users"></i>
                                                        @if($job->applications_count > 0)
                                                            <span class="badge bg-white text-primary ms-1">{{ $job->applications_count }}</span>
                                                        @endif
                                                    </a>
                                                    <a href="{{ route('company.jobs.edit', $job) }}" 
                                                       class="btn btn-sm btn-warning" title="編集">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            title="削除" onclick="confirmDelete({{ $job->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">まだ求人が投稿されていません</h5>
                            <p class="text-muted">最初の求人を投稿してみましょう！</p>
                            <a href="{{ route('company.jobs.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> 新規求人投稿
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 削除確認モーダル -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">求人削除の確認</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>この求人を削除してもよろしいですか？</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>注意:</strong> 削除すると応募情報も全て削除され、元に戻すことはできません。
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">削除する</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .table th {
        border-top: none;
        font-weight: 600;
        background-color: #f8f9fa;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem;
        margin-right: 2px;
    }
    
    .btn-group .btn:not(:first-child) {
        margin-left: 2px;
    }
    
    .card {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .badge {
        font-size: 0.75rem;
    }
</style>
@endsection

@section('scripts')
<script>
function confirmDelete(jobId) {
    const form = document.getElementById('deleteForm');
    form.action = '/company/jobs/' + jobId;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    // 自動リロード（開発用 - 本番では削除）
    // setInterval(() => location.reload(), 30000);
});
</script>
@endsection