@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Danh sách Scenario</h1>
    <a href="{{ route('scenarios.create') }}" class="btn btn-primary mb-3">Thêm Scenario</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Mô tả</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scenarios as $scenario)
                <tr>
                    <td>{{ $scenario->id }}</td>
                    <td>{{ $scenario->name }}</td>
                    <td>{{ $scenario->description }}</td>
                    <td>{{ $scenario->status ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('scenarios.edit', $scenario->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('scenarios.destroy', $scenario->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection