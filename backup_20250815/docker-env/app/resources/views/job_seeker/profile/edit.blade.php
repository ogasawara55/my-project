@extends('layouts.app')

@section('title', 'プロフィール編集')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>プロフィール編集
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('job_seeker.profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- 基本情報 -->
                        <h5 class="border-bottom pb-2 mb-3">基本情報</h5>
                        
                        <!-- 名前 -->
                        <div class="mb-3">
                            <label for="name" class="form-label">名前 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $seeker->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- メールアドレス -->
                        <div class="mb-3">
                            <label for="email" class="form-label">メールアドレス <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $seeker->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- 電話番号 -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">電話番号</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $seeker->phone) }}"
                                   placeholder="例: 090-1234-5678">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">応募時の連絡先として使用されます</div>
                        </div>
                        
                        <!-- プロフィール情報 -->
                        <h5 class="border-bottom pb-2 mb-3 mt-4">プロフィール情報</h5>
                        
                        <!-- 自己PR -->
                        <div class="mb-3">
                            <label for="self_pr" class="form-label">自己PR</label>
                            <textarea class="form-control @error('self_pr') is-invalid @enderror" 
                                      id="self_pr" name="self_pr" rows="4"
                                      placeholder="あなたの強みやスキル、これまでの経験をアピールしてください">{{ old('self_pr', $seeker->self_pr) }}</textarea>
                            @error('self_pr')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">企業にアピールするための自己紹介文を記入してください</div>
                        </div>
                        
                        <!-- 職務経歴 -->
                        <div class="mb-3">
                            <label for="career" class="form-label">職務経歴</label>
                            <textarea class="form-control @error('career') is-invalid @enderror" 
                                      id="career" name="career" rows="6"
                                      placeholder="これまでの職歴、担当業務、使用したスキルなどを具体的に記入してください">{{ old('career', $seeker->career) }}</textarea>
                            @error('career')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">詳細な職務経歴を記入することで、企業からの関心を高めることができます</div>
                        </div>
                        
                        <!-- ボタン -->
                        <div class="d-flex justify-content-between pt-3">
                            <a href="{{ route('job_seeker.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>マイページに戻る
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>プロフィールを更新
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-label {
    font-weight: 600;
    color: #495057;
}

.card {
    border: none;
    border-radius: 10px;
}

.btn {
    border-radius: 6px;
    font-weight: 500;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

textarea.form-control {
    resize: vertical;
}
</style>
@endsection