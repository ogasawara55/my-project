@extends('company.layout')

@section('title', '求人管理')

@section('content')
<div class="container">
    <!-- ページヘッダー -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>
                <i class="fas fa-tachometer-alt me-2"></i>求人管理
            </h2>
            <p class="text-muted">{{ Auth::guard('company')->user()->company_name }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('company.jobs.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-plus me-2"></i>新規求人投稿
            </a>
        </div>
    </div>

    {{-- 成功メッセージ --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- 統計情報 -->
    <div class="row mb-4">
        <div class="col-md-4">
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
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">総応募数</h5>
                            <h2 class="mb-0">{{ $jobs->sum(function($job) { return $job->applications->count(); }) }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">選考中</h5>
                            {{-- 🔥 修正: 文字列 '選考中' → 数値 1 --}}
                            <h2 class="mb-0">{{ $jobs->sum(function($job) { return $job->applications->where('status', 1)->count(); }) }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
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
                <i class="fas fa-list me-2"></i>投稿済み求人一覧
            </h5>
        </div>
        <div class="card-body">
            @if($jobs->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">まだ求人が投稿されていません</h5>
                    <p class="text-muted">最初の求人を投稿してみましょう</p>
                    <a href="{{ route('company.jobs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>求人投稿
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>求人タイトル</th>
                                <th>勤務地</th>
                                <th>雇用形態</th>
                                <th>応募数</th>
                                <th>通過数</th>
                                <th>投稿日</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $job)
                                @php
                                    $totalApplications = $job->applications->count();
                                    // 🔥 修正: 文字列配列 → 数値配列 [1, 2]
                                    $passedApplications = $job->applications->whereIn('status', [1, 2])->count();
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('company.jobs.show', $job) }}" class="text-decoration-none">
                                            <strong>{{ $job->title }}</strong>
                                        </a>
                                    </td>
                                    <td>{{ $job->location }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $job->employment_type }}</span>
                                    </td>
                                    <td>
                                        @if($totalApplications > 0)
                                            <a href="{{ route('company.jobs.applications', $job) }}" class="text-decoration-none">
                                                <span class="badge bg-primary">{{ $totalApplications }}</span>
                                            </a>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $passedApplications }}</span>
                                    </td>
                                    <td>{{ $job->created_at->format('Y/m/d') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('company.jobs.show', $job) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="詳細"
                                               target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('company.jobs.edit', $job) }}" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="編集">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($totalApplications > 0)
                                                <a href="{{ route('company.jobs.applications', $job) }}" 
                                                   class="btn btn-sm btn-outline-success" 
                                                   title="応募者一覧">
                                                    <i class="fas fa-users"></i>
                                                </a>
                                            @endif
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="削除"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal{{ $job->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- 削除確認モーダル -->
                                        <div class="modal fade" id="deleteModal{{ $job->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">求人削除の確認</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>以下の求人を削除してもよろしいですか？</p>
                                                        <p><strong>{{ $job->title }}</strong></p>
                                                        @if($totalApplications > 0)
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                                この求人には{{ $totalApplications }}件の応募があります。<br>
                                                                削除すると応募データも削除されます。
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            キャンセル
                                                        </button>
                                                        <form method="POST" action="{{ route('company.jobs.destroy', $job) }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fas fa-trash me-2"></i>削除
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection