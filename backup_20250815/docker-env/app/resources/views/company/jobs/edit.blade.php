@extends('layouts.app')

@section('title', '求人編集')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- ページヘッダー -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-edit text-warning"></i> 求人編集</h2>
                <a href="{{ route('company.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> 求人管理に戻る
                </a>
            </div>

            <!-- メッセージ表示エリア -->
            <div id="message-area" class="d-none">
                <div id="success-message" class="alert alert-success d-none" role="alert">
                    <i class="fas fa-check-circle"></i> <span id="success-text"></span>
                </div>
                <div id="error-message" class="alert alert-danger d-none" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <span id="error-text"></span>
                </div>
            </div>

            <!-- 編集フォーム -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase"></i> 求人情報の編集
                    </h5>
                </div>
                <div class="card-body">
                   <form id="job-update-form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- 求人タイトル -->
                        <div class="mb-3">
                            <label for="title" class="form-label">
                                <i class="fas fa-heading text-primary"></i> 求人タイトル <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $job->title) }}" 
                                   placeholder="例: Webエンジニア募集"
                                   required>
                            <div class="invalid-feedback" id="title-error"></div>
                        </div>

                        <!-- 仕事内容詳細 -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left text-info"></i> 仕事内容詳細 <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="5" 
                                      placeholder="具体的な業務内容を記載してください"
                                      required>{{ old('description', $job->description) }}</textarea>
                            <div class="invalid-feedback" id="description-error"></div>
                        </div>

                        <!-- 勤務地 -->
                        <div class="mb-3">
                            <label for="location" class="form-label">
                                <i class="fas fa-map-marker-alt text-danger"></i> 勤務地 <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="location" 
                                   name="location" 
                                   value="{{ old('location', $job->location) }}" 
                                   placeholder="例: 東京都渋谷区"
                                   required>
                            <div class="invalid-feedback" id="location-error"></div>
                        </div>

                        <!-- 給与レンジ -->
                        <div class="mb-3">
                            <label for="salary_range" class="form-label">
                                <i class="fas fa-yen-sign text-success"></i> 給与レンジ <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" 
                                    id="salary_range" 
                                    name="salary_range" 
                                    required>
                                <option value="">選択してください</option>
                                <option value="200万円未満" {{ old('salary_range', $job->salary_range) == '200万円未満' ? 'selected' : '' }}>200万円未満</option>
                                <option value="200万円～400万円" {{ old('salary_range', $job->salary_range) == '200万円～400万円' ? 'selected' : '' }}>200万円～400万円</option>
                                <option value="400万円～600万円" {{ old('salary_range', $job->salary_range) == '400万円～600万円' ? 'selected' : '' }}>400万円～600万円</option>
                                <option value="600万円～800万円" {{ old('salary_range', $job->salary_range) == '600万円～800万円' ? 'selected' : '' }}>600万円～800万円</option>
                                <option value="800万円～1000万円" {{ old('salary_range', $job->salary_range) == '800万円～1000万円' ? 'selected' : '' }}>800万円～1000万円</option>
                                <option value="1000万円以上" {{ old('salary_range', $job->salary_range) == '1000万円以上' ? 'selected' : '' }}>1000万円以上</option>
                            </select>
                            <div class="invalid-feedback" id="salary_range-error"></div>
                        </div>

                        <!-- 雇用形態 -->
                        <div class="mb-3">
                            <label for="employment_type" class="form-label">
                                <i class="fas fa-user-tie text-purple"></i> 雇用形態 <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" 
                                    id="employment_type" 
                                    name="employment_type" 
                                    required>
                                <option value="">選択してください</option>
                                <option value="正社員" {{ old('employment_type', $job->employment_type) == '正社員' ? 'selected' : '' }}>正社員</option>
                                <option value="契約社員" {{ old('employment_type', $job->employment_type) == '契約社員' ? 'selected' : '' }}>契約社員</option>
                                <option value="派遣社員" {{ old('employment_type', $job->employment_type) == '派遣社員' ? 'selected' : '' }}>派遣社員</option>
                                <option value="業務委託" {{ old('employment_type', $job->employment_type) == '業務委託' ? 'selected' : '' }}>業務委託</option>
                                <option value="パート・アルバイト" {{ old('employment_type', $job->employment_type) == 'パート・アルバイト' ? 'selected' : '' }}>パート・アルバイト</option>
                                <option value="インターン" {{ old('employment_type', $job->employment_type) == 'インターン' ? 'selected' : '' }}>インターン</option>
                            </select>
                            <div class="invalid-feedback" id="employment_type-error"></div>
                        </div>

                        <!-- イメージ画像 -->
                        <div class="mb-4">
                            <label for="image" class="form-label">
                                <i class="fas fa-image text-secondary"></i> イメージ画像（任意）
                            </label>
                            @if($job->image_url)
                                <div class="mb-2">
                                    <p class="text-muted mb-1">現在の画像:</p>
                                    <img id="current-image" 
                                         src="{{ asset('storage/' . $job->image_url) }}" 
                                         alt="求人画像" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px;">
                                </div>
                            @endif
                            <input type="file" 
                                   class="form-control" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*">
                            <div class="form-text">
                                JPEG、PNG、GIF形式の画像をアップロードできます（最大2MB）
                            </div>
                            <div class="invalid-feedback" id="image-error"></div>
                        </div>

                        <!-- ボタン -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('company.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> キャンセル
                            </a>
                            <div>
                                <button type="submit" class="btn btn-warning me-2" id="update-btn">
                                    <i class="fas fa-save"></i> 更新
                                </button>
                                <a href="{{ route('jobs.show', $job->id) }}" 
                                   class="btn btn-outline-info" 
                                   target="_blank">
                                    <i class="fas fa-external-link-alt"></i> 公開画面を確認
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 求人統計情報 -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-bar"></i> この求人の統計</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h5 class="text-primary">{{ $job->applications->count() }}</h5>
                            <small class="text-muted">総応募数</small>
                        </div>
                        <div class="col-md-4">
                            <h5 class="text-warning">{{ $job->applications->where('status', 1)->count() }}</h5>
                            <small class="text-muted">選考中</small>
                        </div>
                        <div class="col-md-4">
                            <h5 class="text-success">{{ $job->applications->where('status', 2)->count() }}</h5>
                            <small class="text-muted">通過済み</small>
                        </div>
                    </div>
                    @if($job->applications->count() > 0)
                        <div class="text-center mt-3">
                            <a href="{{ route('company.jobs.applications', $job->id) }}" class="btn btn-outline-primary">
                                <i class="fas fa-users"></i> 応募者一覧を見る
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('job-update-form');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // 通常のフォーム送信を防ぐ
        
        // エラー表示をクリア
        clearErrors();
        hideMessages();
        
        // FormDataオブジェクトを作成
        const formData = new FormData(form);
        
        // CSRFトークンを追加
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('_method', 'PUT');
        
        // Ajax送信
        fetch('/company/jobs/{{ $job->id }}', {
    method: 'POST',
    body: formData,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
})
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessMessage(data.message || '求人情報が正常に更新されました。');
                
                // 画像が更新された場合、プレビューを更新
                if (data.image_url && document.getElementById('current-image')) {
                    document.getElementById('current-image').src = data.image_url;
                }
            } else {
                if (data.errors) {
                    showValidationErrors(data.errors);
                } else {
                    showErrorMessage(data.message || '更新に失敗しました。');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage('通信エラーが発生しました。もう一度お試しください。');
        });
    });
    
    function showSuccessMessage(message) {
        const messageArea = document.getElementById('message-area');
        const successMessage = document.getElementById('success-message');
        const successText = document.getElementById('success-text');
        
        successText.textContent = message;
        messageArea.classList.remove('d-none');
        successMessage.classList.remove('d-none');
        
        // 3秒後に自動で非表示
        setTimeout(() => {
            hideMessages();
        }, 3000);
        
        // ページトップにスクロール
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    function showErrorMessage(message) {
        const messageArea = document.getElementById('message-area');
        const errorMessage = document.getElementById('error-message');
        const errorText = document.getElementById('error-text');
        
        errorText.textContent = message;
        messageArea.classList.remove('d-none');
        errorMessage.classList.remove('d-none');
        
        // ページトップにスクロール
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    function showValidationErrors(errors) {
        for (const field in errors) {
            const input = document.getElementById(field);
            const errorDiv = document.getElementById(field + '-error');
            
            if (input && errorDiv) {
                input.classList.add('is-invalid');
                errorDiv.textContent = errors[field][0];
            }
        }
        
        showErrorMessage('入力内容に問題があります。確認してください。');
    }
    
    function clearErrors() {
        const inputs = form.querySelectorAll('.form-control, .form-select');
        inputs.forEach(input => {
            input.classList.remove('is-invalid');
        });
        
        const errorDivs = form.querySelectorAll('.invalid-feedback');
        errorDivs.forEach(div => {
            div.textContent = '';
        });
    }
    
    function hideMessages() {
        const messageArea = document.getElementById('message-area');
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');
        
        messageArea.classList.add('d-none');
        successMessage.classList.add('d-none');
        errorMessage.classList.add('d-none');
    }
});
</script>

@endsection