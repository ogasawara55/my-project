@extends('layouts.app')

@section('title', '企業登録確認')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0 text-center">
                        <i class="fas fa-check-circle text-info"></i> 企業登録確認
                    </h4>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        以下の内容で登録してもよろしいですか？
                    </div>
                    
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light" style="width: 30%;">
                                <i class="fas fa-building text-success"></i> 企業名
                            </th>
                            <td>{{ $validated['company_name'] }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">
                                <i class="fas fa-user text-info"></i> 担当者名
                            </th>
                            <td>{{ $validated['contact_name'] }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">
                                <i class="fas fa-envelope text-warning"></i> メールアドレス
                            </th>
                            <td>{{ $validated['email'] }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">
                                <i class="fas fa-lock text-danger"></i> パスワード
                            </th>
                            <td>********</td>
                        </tr>
                    </table>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <!-- 戻るフォーム -->
                            <form method="GET" action="{{ route('company.register.form') }}">
                                <input type="hidden" name="company_name" value="{{ $validated['company_name'] }}">
                                <input type="hidden" name="contact_name" value="{{ $validated['contact_name'] }}">
                                <input type="hidden" name="email" value="{{ $validated['email'] }}">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="fas fa-arrow-left"></i> 戻って編集
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <!-- 登録実行フォーム -->
                            <form method="POST" action="{{ route('company.register') }}">
                                @csrf
                                <input type="hidden" name="company_name" value="{{ $validated['company_name'] }}">
                                <input type="hidden" name="contact_name" value="{{ $validated['contact_name'] }}">
                                <input type="hidden" name="email" value="{{ $validated['email'] }}">
                                <input type="hidden" name="password" value="{{ $validated['password'] }}">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check"></i> 登録する
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection