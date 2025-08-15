@extends('layouts.app')

@section('title', '求人応募 - {{ $job->title }}')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- パンくずナビ --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('jobs.index') }}">
                            <i class="fas fa-search me-1"></i>求人一覧
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('jobs.show', $job) }}">
                            <i class="fas fa-briefcase me-1"></i>{{ Str::limit($job->title, 30) }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="fas fa-paper-plane me-1"></i>応募
                    </li>
                </ol>
            </nav>

            {{-- 応募確認メッセージ --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>応募完了!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- エラーメッセージ --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>入力内容にエラーがあります:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- 求人情報カード --}}
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase me-2"></i>応募する求人情報
                    </h5>
                </div>
                <div class="card-body">
                    <h4 class="text-primary">{{ $job->title }}</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <i class="fas fa-building text-info me-2"></i>
                                <strong>企業名:</strong> {{ $job->company->company_name }}
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-map-marker-alt text-success me-2"></i>
                                <strong>勤務地:</strong> {{ $job->location }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <i class="fas fa-user-tie text-secondary me-2"></i>
                                <strong>雇用形態:</strong> {{ $job->employment_type }}
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-yen-sign text-warning me-2"></i>
                                <strong>給与:</strong> {{ $job->salary_range }}
                            </p>
                        </div>
                    </div>
                    @if($job->description)
                        <div class="mt-3">
                            <h6><i class="fas fa-info-circle me-2"></i>仕事内容:</h6>
                            <p class="text-muted">{{ Str::limit($job->description, 200) }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 応募フォーム --}}
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-paper-plane me-2"></i>応募フォーム
                    </h4>
                    <small class="d-block mt-1">すべての項目にご記入ください</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('job_seeker.applications.store', $job) }}" novalidate>
                        @csrf
                        <input type="hidden" name="job_id" value="{{ $job->id }}">

                        {{-- 志望動機 --}}
                        <div class="mb-4">
                            <label for="motivation" class="form-label">
                                <i class="fas fa-heart text-danger me-2"></i>志望動機 
                                <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('motivation') is-invalid @enderror" 
                                      id="motivation" 
                                      name="motivation" 
                                      rows="8" 
                                      placeholder="なぜこの求人に応募したいのか、あなたの熱意を詳しく記載してください。

例：
・御社の事業内容に興味を持った理由
・これまでの経験をどう活かせるか
・将来のキャリアビジョン
・この職種を志望する理由
など"
                                      required 
                                      maxlength="2000">{{ old('motivation') }}</textarea>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                具体的で熱意のこもった志望動機を記載してください（最大2000文字）
                                <span id="char-count" class="float-end text-muted">0/2000文字</span>
                            </div>
                            @error('motivation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 連絡先情報 --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope text-primary me-2"></i>連絡先メールアドレス 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', Auth::guard('job_seeker')->user()->email) }}" 
                                           placeholder="example@email.com"
                                           required>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>企業からの連絡先として使用されます
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone text-success me-2"></i>連絡先電話番号 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', Auth::guard('job_seeker')->user()->phone) }}" 
                                           placeholder="090-1234-5678"
                                           pattern="[0-9]{2,4}-[0-9]{2,4}-[0-9]{3,4}"
                                           required>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>緊急時の連絡先として使用されます
                                    </div>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- プロフィール情報表示 --}}
                        @if(Auth::guard('job_seeker')->user()->self_pr || Auth::guard('job_seeker')->user()->career)
                            <div class="alert alert-info">
                                <h6><i class="fas fa-user me-2"></i>あなたのプロフィール情報も企業に送信されます</h6>
                                @if(Auth::guard('job_seeker')->user()->self_pr)
                                    <p class="mb-1"><strong>自己PR:</strong> {{ Str::limit(Auth::guard('job_seeker')->user()->self_pr, 100) }}</p>
                                @endif
                                @if(Auth::guard('job_seeker')->user()->career)
                                    <p class="mb-1"><strong>職務経歴:</strong> {{ Str::limit(Auth::guard('job_seeker')->user()->career, 100) }}</p>
                                @endif
                                <small class="text-muted">
                                    <i class="fas fa-edit me-1"></i>
                                    プロフィール情報は
                                    <a href="{{ route('job_seeker.profile.edit') }}" target="_blank">マイページ</a>
                                    から編集できます
                                </small>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>プロフィール情報が未設定です</h6>
                                <p class="mb-1">自己PRや職務経歴を設定すると、より効果的な応募ができます。</p>
                                <a href="{{ route('job_seeker.profile.edit') }}" target="_blank" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit me-1"></i>プロフィールを編集する
                                </a>
                            </div>
                        @endif

                        {{-- 確認事項 --}}
                        <div class="alert alert-light border-warning">
                            <h6><i class="fas fa-exclamation-circle text-warning me-2"></i>応募前の確認事項</h6>
                            <ul class="mb-0">
                                <li>記載内容に間違いがないかご確認ください</li>
                                <li>企業担当者から上記の連絡先にご連絡させていただく場合があります</li>
                                <li>応募後の取り消しはできませんのでご注意ください</li>
                                <li>応募情報は企業にのみ送信され、第三者に提供されることはありません</li>
                            </ul>
                        </div>

                        {{-- 同意チェックボックス --}}
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="agree" name="agree" required>
                                <label class="form-check-label" for="agree">
                                    <strong>上記の確認事項に同意して応募します</strong>
                                </label>
                            </div>
                        </div>

                        {{-- ボタン --}}
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('jobs.show', $job) }}" class="btn btn-outline-secondary btn-lg me-md-2">
                                <i class="fas fa-arrow-left me-2"></i>求人詳細に戻る
                            </a>
                            <button type="submit" class="btn btn-success btn-lg" id="submit-btn">
                                <i class="fas fa-paper-plane me-2"></i>この求人に応募する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 文字数カウンター
    const motivationTextarea = document.getElementById('motivation');
    const charCount = document.getElementById('char-count');
    
    motivationTextarea.addEventListener('input', function() {
        const currentLength = this.value.length;
        charCount.textContent = `${currentLength}/2000文字`;
        
        if (currentLength > 2000) {
            charCount.classList.add('text-danger');
        } else if (currentLength > 1800) {
            charCount.classList.add('text-warning');
            charCount.classList.remove('text-danger');
        } else {
            charCount.classList.remove('text-danger', 'text-warning');
        }
    });

    // 初期文字数表示
    motivationTextarea.dispatchEvent(new Event('input'));

    // 電話番号フォーマット
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function() {
        let value = this.value.replace(/[^\d]/g, '');
        if (value.length > 0) {
            if (value.length <= 4) {
                value = value;
            } else if (value.length <= 8) {
                value = value.slice(0, 4) + '-' + value.slice(4);
            } else {
                value = value.slice(0, 4) + '-' + value.slice(4, 8) + '-' + value.slice(8, 12);
            }
        }
        this.value = value;
    });

    // フォーム送信前の確認
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submit-btn');
    
    form.addEventListener('submit', function(e) {
        const agreeCheckbox = document.getElementById('agree');
        
        if (!agreeCheckbox.checked) {
            e.preventDefault();
            alert('確認事項に同意してください。');
            return false;
        }

        // 二重送信防止
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>応募中...';
        
        // 5秒後に再度有効化（エラーの場合のため）
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>この求人に応募する';
        }, 5000);
    });
});
</script>

<style>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.btn-success {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

#motivation {
    resize: vertical;
    min-height: 150px;
}

.breadcrumb-item.active {
    font-weight: 600;
}
</style>
@endsection