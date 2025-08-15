@extends('layouts.app')

@section('title', '企業ログイン')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0 text-center">
                        <i class="fas fa-building text-success"></i> 企業ログイン
                    </h4>
                    <p class="text-center mb-0 mt-2">求人投稿・応募者管理はこちらから</p>
                </div>

                <div class="card-body">
                    {{-- エラーメッセージ表示 --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- 成功メッセージ表示 --}}
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('company.login') }}">
                        @csrf

                        {{-- メールアドレス --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope text-primary"></i> メールアドレス
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="company@example.com"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- パスワード --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock text-primary"></i> パスワード
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="パスワードを入力"
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ログイン保持 --}}
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                ログイン状態を保持する
                            </label>
                        </div>

                        {{-- ログインボタン --}}
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt"></i> ログイン
                            </button>
                        </div>

                        {{-- リンク類 --}}
                        <div class="text-center">
                            <div class="mb-2">
                                <a href="{{ route('company.password.reset') }}" class="text-decoration-none">
                                    <i class="fas fa-key"></i> パスワードを忘れた方
                                </a>
                            </div>
                            <div>
                                <small class="text-muted">
                                    アカウントをお持ちでない方は 
                                    <a href="{{ route('company.register.form') }}" class="text-decoration-none">
                                        こちらから新規登録
                                    </a>
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 求職者ログインリンク --}}
            <div class="text-center mt-3">
                <small class="text-muted">
                    求職者の方はこちら → 
                    <a href="{{ route('job_seeker.login.form') }}" class="text-decoration-none">
                        <i class="fas fa-user"></i> 求職者ログイン
                    </a>
                </small>
            </div>
        </div>
    </div>
</div>
@endsection