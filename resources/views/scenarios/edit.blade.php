@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Chỉnh sửa Scenario</h1>
    <form action="{{ route('scenarios.update', $scenario->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Tên</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $scenario->name }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea name="description" id="description" class="form-control">{{ $scenario->description }}</textarea>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Trạng thái</label>
            <select name="status" id="status" class="form-control">
                <option value="1" {{ $scenario->status ? 'selected' : '' }}>Active</option>
                <option value="0" {{ !$scenario->status ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Cập nhật</button>
    </form>
</div>
@endsection