@extends('layouts.company')

@section('title', '応募者一覧')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- ヘッダー部分 -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-2">応募者一覧</h2>
                    <h3 class="h5 text-muted">{{ $job->title }}</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.dashboard') }}">ダッシュボード</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.jobs.show', $job) }}">{{ Str::limit($job->title, 30) }}</a>
                            </li>
                            <li class="breadcrumb-item active">応募者一覧</li>
                        </ol>
                    </nav>
                </div>
                <div class="text-end">
                    <a href="{{ route('company.jobs.show', $job) }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> 求人詳細に戻る
                    </a>
                    <a href="{{ route('company.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-home"></i> ダッシュボード
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
                                    <h5 class="card-title">応募合計</h5>
                                    <h2 class="mb-0">{{ $applications->count() }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
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
                                    <h5 class="card-title">通過合計</h5>
                                    <h2 class="mb-0">{{ $applications->whereIn('status', [1, 2])->count() }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
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
                                    <h2 class="mb-0">{{ $applications->where('status', 1)->count() }}</h2>
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
                                    <h5 class="card-title">結果通知済</h5>
                                    <h2 class="mb-0">{{ $applications->where('status', 2)->count() }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-envelope fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 応募者一覧テーブル -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> 応募者一覧
                        @if($applications->count() > 0)
                            <span class="badge bg-secondary ms-2">{{ $applications->count() }}件</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($applications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">応募者名</th>
                                        <th scope="col">連絡先</th>
                                        <th scope="col">応募日時</th>
                                        <th scope="col">ステータス</th>
                                        <th scope="col">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $application)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2">
                                                        {{ mb_substr($application->jobSeeker->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $application->jobSeeker->name }}</strong>
                                                        @if($application->jobSeeker->self_pr)
                                                            <br><small class="text-muted">
                                                                <i class="fas fa-star"></i> 自己PR有
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <a href="mailto:{{ $application->email }}" class="text-decoration-none">
                                                        <i class="fas fa-envelope"></i> {{ $application->email }}
                                                    </a>
                                                </div>
                                                <div class="mt-1">
                                                    <a href="tel:{{ $application->phone }}" class="text-decoration-none">
                                                        <i class="fas fa-phone"></i> {{ $application->phone }}
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">
                                                    {{ $application->applied_at->format('Y/m/d') }}<br>
                                                    <small>{{ $application->applied_at->format('H:i') }}</small>
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClasses = [
                                                        0 => 'bg-secondary',
                                                        1 => 'bg-warning',
                                                        2 => 'bg-success'
                                                    ];
                                                    $statusTexts = [
                                                        0 => '応募済',
                                                        1 => '選考中',
                                                        2 => '結果通知済'
                                                    ];
                                                @endphp
                                                
                                                <form method="POST" action="{{ route('company.applications.update.status', $application) }}" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="status" class="form-select form-select-sm status-select" 
                                                            onchange="this.form.submit()" style="min-width: 120px;">
                                                        @foreach($statusTexts as $value => $text)
                                                            <option value="{{ $value }}" 
                                                                    {{ $application->status == $value ? 'selected' : '' }}>
                                                                {{ $text }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('company.applications.show', $application) }}" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> 詳細
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">まだ応募がありません</h5>
                            <p class="text-muted">この求人への応募をお待ちください。</p>
                            <a href="{{ route('company.jobs.show', $job) }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> 求人詳細に戻る
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 求人情報サマリー -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase"></i> 求人情報
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">求人タイトル</label>
                                <div>{{ $job->title }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">勤務地</label>
                                <div>{{ $job->location }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">給与レンジ</label>
                                <div>{{ $job->salary_range }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">雇用形態</label>
                                <div>{{ $job->employment_type }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('company.jobs.edit', $job) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i> 求人を編集
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .status-select {
        cursor: pointer;
    }
    
    .status-select:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .card-body .form-label {
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        background-color: #f8f9fa;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .avatar-circle {
        font-size: 1.1rem;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem;
    }
    
    .card {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ステータス変更時の確認
    const statusSelects = document.querySelectorAll('.status-select');
    
    statusSelects.forEach(function(select) {
        let originalValue = select.value;
        
        select.addEventListener('change', function() {
            const statusTexts = {
                '0': '応募済',
                '1': '選考中',
                '2': '結果通知済'
            };
            
            if (!confirm(`ステータスを「${statusTexts[this.value]}」に変更しますか？`)) {
                this.value = originalValue;
                return;
            }
            
            // 変更が確定した場合は originalValue を更新
            originalValue = this.value;
        });
    });
});
</script>
@endsection