@extends('layouts.app')

@section('title', '登録内容確認 - 求人応募ポータル')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-warning text-dark text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>登録内容確認
                    </h4>
                    <small>以下の内容で登録します。間違いがないか確認してください。</small>
                </div>
                
                <div class="card-body p-4">
                    {{-- エラーメッセージ --}}
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif


                    {{-- 登録情報表示 --}}
                    @if(isset($data))
                    <div class="row">
                        <div class="col-12">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">
                                        <i class="fas fa-user text-primary me-2"></i>登録情報
                                    </h5>
                                    
                                    <table class="table table-borderless">
                                        <tr>
                                            <th class="text-muted" style="width: 30%;">
                                                <i class="fas fa-user me-2"></i>氏名
                                            </th>
                                            <td><strong class="text-dark">{{ $data['name'] ?? 'データなし' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">
                                                <i class="fas fa-envelope me-2"></i>メールアドレス
                                            </th>
                                            <td><strong class="text-dark">{{ $data['email'] ?? 'データなし' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">
                                                <i class="fas fa-lock me-2"></i>パスワード
                                            </th>
                                            <td>
                                                <span class="text-muted">●●●●●●●●</span> 
                                                <small class="text-muted">（セキュリティのため非表示）</small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- アクションボタン --}}
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('job_seeker.register.form') }}" class="btn btn-outline-secondary btn-lg w-100">
                                <i class="fas fa-arrow-left me-2"></i>戻って修正
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <form method="POST" action="{{ route('job_seeker.register.execute') }}" class="d-inline w-100">
                                @csrf
                                {{-- 隠しフィールドでデータを送信 --}}
                                <input type="hidden" name="name" value="{{ $data['name'] ?? '' }}">
                                <input type="hidden" name="email" value="{{ $data['email'] ?? '' }}">
                                
                                <button type="submit" class="btn btn-success btn-lg w-100" id="registerButton">
                                    <i class="fas fa-check me-2"></i>この内容で登録
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>データが見つかりません</h6>
                        <p>登録データが正しく受信されませんでした。</p>
                        <a href="{{ route('job_seeker.register.form') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>登録画面に戻る
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- トップページリンク --}}
            <div class="text-center mt-3">
                <a href="{{ route('welcome') }}" class="text-muted text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i>トップページに戻る
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 15px;
}

.btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 確認画面が正常に読み込まれました');
    
    const registerButton = document.getElementById('registerButton');
    if (registerButton) {
        const form = registerButton.closest('form');
        
        form.addEventListener('submit', function(e) {
            registerButton.disabled = true;
            registerButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>登録中...';
            
            if (!confirm('この内容で登録を実行しますか？')) {
                e.preventDefault();
                registerButton.disabled = false;
                registerButton.innerHTML = '<i class="fas fa-check me-2"></i>この内容で登録';
                return false;
            }
        });
    }
});
</script>
@endsection