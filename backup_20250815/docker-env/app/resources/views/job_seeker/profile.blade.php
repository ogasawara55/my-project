<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール編集 - 求人応募ポータル</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- ナビゲーションバー -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-briefcase me-2"></i>求人応募ポータル
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>{{ $jobSeeker->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('job_seeker.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>マイページ
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('job_seeker.logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>ログアウト
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0">プロフィール編集</h4>
                        <small>あなたの情報を更新してください</small>
                    </div>
                    <div class="card-body">
                        {{-- 成功メッセージ --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- エラーメッセージ --}}
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- バリデーションエラー --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>入力内容にエラーがあります</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('job_seeker.profile.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">氏名 <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $jobSeeker->name) }}" 
                                               required 
                                               maxlength="100">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">メールアドレス <span class="text-danger">*</span></label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', $jobSeeker->email) }}" 
                                               required 
                                               maxlength="255">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">電話番号</label>
                                        <input type="tel" 
                                               class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" 
                                               name="phone" 
                                               value="{{ old('phone', $jobSeeker->phone) }}" 
                                               placeholder="090-1234-5678"
                                               maxlength="20">
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i> 企業からの連絡に使用されます
                                        </div>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="self_pr" class="form-label">自己PR</label>
                                        <textarea class="form-control @error('self_pr') is-invalid @enderror" 
                                                  id="self_pr" 
                                                  name="self_pr" 
                                                  rows="4" 
                                                  placeholder="あなたの強みやアピールポイントを記入してください">{{ old('self_pr', $jobSeeker->self_pr) }}</textarea>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i> 企業に対するアピールポイントを記入してください
                                        </div>
                                        @error('self_pr')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="career" class="form-label">職務経歴</label>
                                        <textarea class="form-control @error('career') is-invalid @enderror" 
                                                  id="career" 
                                                  name="career" 
                                                  rows="6" 
                                                  placeholder="これまでの職務経歴を時系列で記入してください">{{ old('career', $jobSeeker->career) }}</textarea>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i> 過去の職歴、経験、スキルなどを記入してください
                                        </div>
                                        @error('career')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <h6><i class="fas fa-lightbulb me-2"></i>プロフィール充実のコツ</h6>
                                <ul class="mb-0 small">
                                    <li><strong>自己PR:</strong> あなたの強み、特技、目標などを具体的に</li>
                                    <li><strong>職務経歴:</strong> 会社名、職種、期間、主な業務内容を時系列で</li>
                                    <li><strong>電話番号:</strong> 企業からの連絡がスムーズになります</li>
                                </ul>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('job_seeker.dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>マイページに戻る
                                </a>
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-save me-2"></i>更新する
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>