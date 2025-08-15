@extends('company.layout')

@section('title', 'プロフィール編集')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5>プロフィール編集</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('company.profile.update') }}">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">企業名</label>
                    <input type="text" class="form-control" name="company_name" 
                           value="{{ $company->company_name }}" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">担当者名</label>
                    <input type="text" class="form-control" name="contact_name" 
                           value="{{ $company->contact_name }}" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">メールアドレス</label>
                    <input type="email" class="form-control" name="email" 
                           value="{{ $company->email }}" required>
                </div>
                
                <button type="submit" class="btn btn-primary">更新</button>
            </form>
        </div>
    </div>
</div>
@endsection