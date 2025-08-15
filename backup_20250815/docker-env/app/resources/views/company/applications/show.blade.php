@extends('layouts.company')

@section('title', '応募者詳細')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- ヘッダー部分 -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-2">応募者詳細</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.dashboard') }}">ダッシュボード</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.jobs.show', $application->job) }}">{{ Str::limit($application->job->title, 30) }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.jobs.applications', $application->job) }}">応募者一覧</a>
                            </li>
                            <li class="breadcrumb-item active">応募者詳細</li>
                        </ol>
                    </nav>
                </div>
                <div class="text-end">
                    <a href="{{ route('company.jobs.applications', $application->job) }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> 応募者一覧に戻る
                    </a>
                    <a href="{{ route('company.jobs.show', $application->job) }}" class="btn btn-outline-primary">
                        <i class="fas fa-briefcase"></i> 求人詳細
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- 応募者基本情報 -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user"></i> 応募者基本情報
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="avatar-large mx-auto mb-3">
                                    {{ mb_substr($application->jobSeeker->name, 0, 1) }}
                                </div>
                                <h4>{{ $application->jobSeeker->name }}</h4>
                                
                                @php
                                    $statusClasses = [
                                        0 => 'bg-secondary',
                                        1 => 'bg-warning text-dark',
                                        2 => 'bg-success'
                                    ];
                                    $statusTexts = [
                                        0 => '応募済',
                                        1 => '選考中',
                                        2 => '結果通知済'
                                    ];
                                @endphp
                                
                                <span class="badge {{ $statusClasses[$application->status] }} fs-6">
                                    {{ $statusTexts[$application->status] }}
                                </span>
                            </div>

                            <div class="application-info">
                                <div class="info-item">
                                    <label class="info-label">応募時メールアドレス</label>
                                    <div class="info-value">
                                        <a href="mailto:{{ $application->email }}">
                                            <i class="fas fa-envelope me-1"></i>
                                            {{ $application->email }}
                                        </a>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <label class="info-label">応募時電話番号</label>
                                    <div class="info-value">
                                        <a href="tel:{{ $application->phone }}">
                                            <i class="fas fa-phone me-1"></i>
                                            {{ $application->phone }}
                                        </a>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <label class="info-label">応募日時</label>
                                    <div class="info-value">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $application->applied_at->format('Y年m月d日 H:i') }}
                                    </div>
                                </div>

                                @if($application->jobSeeker->email !== $application->email)
                                <div class="info-item">
                                    <label class="info-label">登録メールアドレス</label>
                                    <div class="info-value">
                                        <i class="fas fa-envelope me-1"></i>
                                        {{ $application->jobSeeker->email }}
                                    </div>
                                </div>
                                @endif

                                @if($application->jobSeeker->phone && $application->jobSeeker->phone !== $application->phone)
                                <div class="info-item">
                                    <label class="info-label">登録電話番号</label>
                                    <div class="info-value">
                                        <i class="fas fa-mobile-alt me-1"></i>
                                        {{ $application->jobSeeker->phone }}
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- ステータス変更 -->
                            <div class="mt-4">
                                <form method="POST" action="{{ route('company.applications.update.status', $application) }}">
                                    @csrf
                                    @method('PUT')
                                    <label class="form-label">ステータス変更</label>
                                    <div class="d-flex">
                                        <select class="form-select me-2" name="status" id="statusSelect">
                                            @foreach($statusTexts as $value => $text)
                                                <option value="{{ $value }}" 
                                                        {{ $application->status == $value ? 'selected' : '' }}>
                                                    {{ $text }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> 更新
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 応募内容・プロフィール詳細 -->
                <div class="col-lg-8">
                    <!-- 志望動機 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-heart"></i> 志望動機
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="motivation-content">
                                {!! nl2br(e($application->motivation)) !!}
                            </div>
                        </div>
                    </div>

                    <!-- 自己PR -->
                    @if($application->jobSeeker->self_pr)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-star"></i> 自己PR
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="self-pr-content">
                                {!! nl2br(e($application->jobSeeker->self_pr)) !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- 職務経歴 -->
                    @if($application->jobSeeker->career)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-briefcase"></i> 職務経歴
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="career-content">
                                {!! nl2br(e($application->jobSeeker->career)) !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- プロフィール情報がない場合 -->
                    @if(!$application->jobSeeker->self_pr && !$application->jobSeeker->career)
                    <div class="card mb-4">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                            <h6 class="text-muted">追加プロフィール情報</h6>
                            <p class="text-muted mb-0">
                                この応募者は自己PRや職務経歴を登録していません。
                            </p>
                        </div>
                    </div>
                    @endif

                    <!-- 応募求人情報（参考） -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle"></i> 応募求人情報
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="info-label">求人タイトル</label>
                                    <div class="info-value">{{ $application->job->title }}</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="info-label">勤務地</label>
                                        <div class="info-value">{{ $application->job->location }}</div>
                                    </div>
                                    <div class="info-item">
                                        <label class="info-label">給与レンジ</label>
                                        <div class="info-value">{{ $application->job->salary_range }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="info-label">雇用形態</label>
                                        <div class="info-value">{{ $application->job->employment_type }}</div>
                                    </div>
                                    <div class="info-item">
                                        <label class="info-label">投稿日</label>
                                        <div class="info-value">{{ $application->job->created_at->format('Y年m月d日') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">求人管理</span>
                                    <div>
                                        <a href="{{ route('company.jobs.show', $application->job) }}" class="btn btn-outline-info btn-sm me-2">
                                            <i class="fas fa-eye"></i> 求人詳細
                                        </a>
                                        <a href="{{ route('company.jobs.edit', $application->job) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-edit"></i> 求人編集
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
</div>
@endsection

@section('styles')
<style>
    .avatar-large {
        width: 80px;
        height: 80px;
        background-color: #007bff;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 2rem;
    }

    .application-info .info-item {
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .application-info .info-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
        display: block;
    }

    .info-value {
        color: #212529;
        font-size: 1rem;
    }

    .info-value a {
        color: #0d6efd;
        text-decoration: none;
    }

    .info-value a:hover {
        text-decoration: underline;
    }

    .motivation-content,
    .self-pr-content,
    .career-content {
        line-height: 1.7;
        font-size: 1rem;
        color: #212529;
        white-space: pre-wrap;
    }

    .card {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .breadcrumb {
        background: none;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: ">";
    }

    .badge.fs-6 {
        font-size: 1rem !important;
        padding: 0.5rem 0.75rem;
    }

    #statusSelect {
        min-width: 120px;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
    }

    .border-top {
        border-top: 1px solid #dee2e6 !important;
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ステータス変更時の確認
    const statusForm = document.querySelector('form');
    const statusSelect = document.getElementById('statusSelect');
    let originalValue = statusSelect.value;
    
    statusForm.addEventListener('submit', function(e) {
        const statusTexts = {
            '0': '応募済',
            '1': '選考中',
            '2': '結果通知済'
        };
        
        const newStatus = statusSelect.value;
        
        if (newStatus !== originalValue) {
            if (!confirm(`ステータスを「${statusTexts[newStatus]}」に変更しますか？`)) {
                e.preventDefault();
                statusSelect.value = originalValue;
                return;
            }
        }
    });
    
    // メールリンクと電話リンクのクリック追跡
    document.querySelectorAll('a[href^="mailto:"], a[href^="tel:"]').forEach(function(link) {
        link.addEventListener('click', function() {
            console.log('Contact link clicked:', this.href);
        });
    });
});
</script>
@endsection