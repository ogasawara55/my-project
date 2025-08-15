@extends('layouts.app')

@section('title', 'ブックマーク一覧')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <!-- ページヘッダー -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-bookmark text-primary me-2"></i>ブックマーク一覧</h2>
                <div>
                    <a href="{{ route('jobs.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-search me-2"></i>求人を探す
                    </a>
                    <a href="{{ route('job_seeker.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>マイページに戻る
                    </a>
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

            <!-- ブックマーク統計 -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="card-title text-primary">
                                <i class="fas fa-bookmark fa-2x"></i>
                            </h5>
                            <h3 class="text-primary">{{ $bookmarks->total() ?? 0 }}件</h3>
                            <p class="card-text text-muted">ブックマーク中</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($bookmarks && $bookmarks->count() > 0)
                <!-- ブックマーク一覧 -->
                <div class="row">
                    @foreach($bookmarks as $bookmark)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                @if($bookmark->job->image_url)
                                    <img src="{{ $bookmark->job->image_url }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="求人画像">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-building fa-3x text-muted"></i>
                                    </div>
                                @endif
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $bookmark->job->title }}</h5>
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-building me-1"></i>{{ $bookmark->job->company->company_name }}
                                    </h6>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $bookmark->job->location }}
                                        </small><br>
                                        <small class="text-muted">
                                            <i class="fas fa-yen-sign me-1"></i>{{ $bookmark->job->salary_range }}
                                        </small><br>
                                        <small class="text-muted">
                                            <i class="fas fa-briefcase me-1"></i>{{ $bookmark->job->employment_type }}
                                        </small><br>
                                        <small class="text-success">
                                            <i class="fas fa-bookmark me-1"></i>{{ $bookmark->bookmarked_at->format('Y/m/d H:i') }} にブックマーク
                                        </small>
                                    </p>
                                    <div class="mt-auto">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('jobs.show', $bookmark->job->id) }}" class="btn btn-primary flex-fill">
                                                <i class="fas fa-eye me-1"></i>詳細を見る
                                            </a>
                                            <form action="{{ route('job_seeker.bookmarks.destroy.by.id', $bookmark->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ブックマークを削除しますか？')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- ページネーション -->
                @if(method_exists($bookmarks, 'links'))
                    <div class="d-flex justify-content-center mt-4">
                        {{ $bookmarks->links() }}
                    </div>
                @endif

            @else
                <!-- ブックマークが空の場合 -->
                <div class="text-center py-5">
                    <i class="fas fa-bookmark fa-5x text-muted mb-3"></i>
                    <h4 class="text-muted">まだブックマークがありません</h4>
                    <p class="text-muted mb-4">気になる求人をブックマークして、後で簡単にアクセスできるようにしましょう。</p>
                    <a href="{{ route('jobs.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-search me-2"></i>求人を探す
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.btn {
    transition: all 0.2s;
}
</style>
@endsection