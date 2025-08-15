@extends('layouts.app')

@section('title', '退会手続き')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">退会手続き</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong>注意事項</strong>
                        <ul class="mb-0 mt-2">
                            <li>退会すると、アカウント情報と応募履歴がすべて削除されます</li>
                            <li>ブックマークした求人情報も削除されます</li>
                            <li>一度退会すると、データの復旧はできません</li>
                            <li>現在の応募件数: {{ $applicationCount }}件</li>
                            <li>現在のブックマーク件数: {{ $bookmarkCount }}件</li>
                        </ul>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- ★修正1: 正しいルート名に変更 --}}
                    <form method="POST" action="{{ route('job_seeker.withdraw') }}">
                        @csrf
                        {{-- ★修正2: DELETEメソッドを追加 --}}
                        @method('DELETE')

                        <!-- 退会理由（任意） -->
                        <div class="form-group mb-3">
                            <label for="reason" class="form-label">退会理由（任意）</label>
                            <textarea name="reason" id="reason" class="form-control" rows="4" 
                                      placeholder="退会理由をお聞かせください（サービス改善のため）">{{ old('reason') }}</textarea>
                        </div>

                        {{-- ★修正3: feedback フィールドを追加（コントローラーが期待している） --}}
                        <div class="form-group mb-3">
                            <label for="feedback" class="form-label">ご意見・ご要望（任意）</label>
                            <textarea name="feedback" id="feedback" class="form-control" rows="3" 
                                      placeholder="サービスについてのご意見をお聞かせください">{{ old('feedback') }}</textarea>
                        </div>

                        {{-- ★修正4: confirm チェックボックスを追加（コントローラーが必須としている） --}}
                        <div class="form-group mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="confirm" id="confirm" class="form-check-input" value="1" required>
                                <label for="confirm" class="form-check-label">
                                    <span class="text-danger">*</span> 上記の注意事項を理解し、退会に同意します
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('job_seeker.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> マイページに戻る
                            </a>
                            
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('本当に退会しますか？この操作は取り消せません。')">
                                <i class="fas fa-user-times"></i> 退会する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// 退会ボタンのダブルクリック防止
document.querySelector('form').addEventListener('submit', function(e) {
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 処理中...';
});
</script>
@endsection