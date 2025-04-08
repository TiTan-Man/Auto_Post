@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Lấy thông tin tương tác bài viết Facebook</h2>
    <form action="{{ route('facebook.insights') }}" method="POST" class="mb-4">
        @csrf
        <div class="form-group">
            <label for="page_id">Page ID:</label>
            <input type="text" name="page_id" id="page_id" class="form-control" placeholder="Nhập Page ID" value="{{ old('pageId') ?? '' }}" required>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Lấy dữ liệu</button>
    </form>

    @if(isset($posts))
        <h4>Kết quả từ Facebook:</h4>
        @foreach($posts as $post)
            <div class="card mb-3">
                <div class="card-body">
                    <p><strong>Nội dung:</strong> {{ $post['message'] ?? '[Không có nội dung]' }}</p>
                    <p><strong>Ngày đăng:</strong> {{ $post['created_time'] }}</p>
                    <p><strong>Cảm xúc:</strong> {{ $post['reactions']['summary']['total_count'] ?? 0 }}</p>
                    <p><strong>Bình luận:</strong> {{ $post['comments']['summary']['total_count'] ?? 0 }}</p>
                </div>
            </div>
            @if(isset($post['reaction_types']))
    <p><strong>Chi tiết cảm xúc:</strong></p>
    <ul>
        @foreach($post['reaction_types'] as $type => $count)
            <li>{{ ucfirst($type) }}: {{ $count }}</li>
        @endforeach
    </ul>
@endif

        @endforeach
    @endif
</div>
@endsection
