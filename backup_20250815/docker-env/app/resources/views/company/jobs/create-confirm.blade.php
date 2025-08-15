@extends('layouts.app')

@section('title', '求人投稿確認')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0 text-center">
                        <i class="fas fa-check-circle text-info"></i> 求人投稿確認
                    </h4>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        以下の内容で求人を投稿してもよろしいですか？
                    </div>
                    
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light" style="width: 20%;">
                                <i class="fas fa-briefcase text-primary"></i> 求人タイトル
                            </th>
                            <td>{{ $validated['title'] }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">
                                <i class="fas fa-file-alt text-info"></i> 仕事内容詳細
                            </th>
                            <td>{!! nl2br(e($validated['description'])) !!}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">
                                <i class="fas fa-map-marker-alt text-warning"></i> 勤務地
                            </th>
                            <td>{{ $validated['location'] }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">
                                <i class="fas fa-yen-sign text-success"></i> 給与レンジ
                            </th>
                            <td>{{ $validated['salary_range'] }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">
                                <i class="fas fa-user-tie text-secondary"></i> 雇用形態
                            </th>
                            <td>{{ $validated['employment_type'] }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">
                                <i class="fas fa-image text-primary"></i> イメージ画像URL
                            </th>
                            <td>
                                @if(!empty($validated['image_url']))
                                    <a href="{{ $validated['image_url'] }}" target="_blank">{{ $validated['image_url'] }}</a>
                                @else
                                    <span class="text-muted">未設定</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <!-- 戻るフォーム -->
                            <form method="GET" action="{{ route('company.jobs.create') }}">
                                <input type="hidden" name="title" value="{{ $validated['title'] }}">
                                <input type="hidden" name="description" value="{{ $validated['description'] }}">
                                <input type="hidden" name="location" value="{{ $validated['location'] }}">
                                <input type="hidden" name="salary_range" value="{{ $validated['salary_range'] }}">
                                <input type="hidden" name="employment_type" value="{{ $validated['employment_type'] }}">
                                <input type="hidden" name="image_url" value="{{ $validated['image_url'] ?? '' }}">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="fas fa-arrow-left"></i> 戻って編集
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <!-- 投稿実行フォーム -->
                            <form method="POST" action="{{ route('company.jobs.store') }}">
                                @csrf
                                <input type="hidden" name="title" value="{{ $validated['title'] }}">
                                <input type="hidden" name="description" value="{{ $validated['description'] }}">
                                <input type="hidden" name="location" value="{{ $validated['location'] }}">
                                <input type="hidden" name="salary_range" value="{{ $validated['salary_range'] }}">
                                <input type="hidden" name="employment_type" value="{{ $validated['employment_type'] }}">
                                <input type="hidden" name="image_url" value="{{ $validated['image_url'] ?? '' }}">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check"></i> 求人を投稿する
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