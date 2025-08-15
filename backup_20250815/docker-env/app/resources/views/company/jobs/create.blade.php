@extends('company.layout')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">新規求人投稿</h4>
                </div>
                <div class="card-body">
                    {{-- ⚠️ 重要修正: actionを正しいURLに変更 --}}
                    <form method="POST" action="{{ route('company.jobs.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">求人タイトル <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">仕事内容詳細 <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">勤務地 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                   id="location" name="location" value="{{ old('location') }}" required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="salary_range" class="form-label">給与レンジ <span class="text-danger">*</span></label>
                                    <select class="form-select @error('salary_range') is-invalid @enderror" 
                                            id="salary_range" name="salary_range" required>
                                        <option value="">選択してください</option>
                                        <option value="200万円未満" {{ old('salary_range') == '200万円未満' ? 'selected' : '' }}>200万円未満</option>
                                        <option value="200万円～400万円" {{ old('salary_range') == '200万円～400万円' ? 'selected' : '' }}>200万円～400万円</option>
                                        <option value="400万円～600万円" {{ old('salary_range') == '400万円～600万円' ? 'selected' : '' }}>400万円～600万円</option>
                                        <option value="600万円～800万円" {{ old('salary_range') == '600万円～800万円' ? 'selected' : '' }}>600万円～800万円</option>
                                        <option value="800万円～1000万円" {{ old('salary_range') == '800万円～1000万円' ? 'selected' : '' }}>800万円～1000万円</option>
                                        <option value="1000万円以上" {{ old('salary_range') == '1000万円以上' ? 'selected' : '' }}>1000万円以上</option>
                                    </select>
                                    @error('salary_range')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employment_type" class="form-label">雇用形態 <span class="text-danger">*</span></label>
                                    <select class="form-select @error('employment_type') is-invalid @enderror" 
                                            id="employment_type" name="employment_type" required>
                                        <option value="">選択してください</option>
                                        <option value="正社員" {{ old('employment_type') == '正社員' ? 'selected' : '' }}>正社員</option>
                                        <option value="契約社員" {{ old('employment_type') == '契約社員' ? 'selected' : '' }}>契約社員</option>
                                        <option value="業務委託" {{ old('employment_type') == '業務委託' ? 'selected' : '' }}>業務委託</option>
                                        <option value="アルバイト・パート" {{ old('employment_type') == 'アルバイト・パート' ? 'selected' : '' }}>アルバイト・パート</option>
                                        <option value="インターン" {{ old('employment_type') == 'インターン' ? 'selected' : '' }}>インターン</option>
                                    </select>
                                    @error('employment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">イメージ画像</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            <div class="form-text">JPG、PNG、GIF形式の画像をアップロードできます（最大2MB）</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('company.jobs.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> 戻る
                            </a>
                            <button type="submit" class="btn btn-primary">
                                確認画面へ <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection