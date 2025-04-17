@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Lấy Thông Tin Facebook Insights</h2>
    <form action="{{ route('facebook.insights') }}" method="POST" class="mb-4">
        @csrf
        <div class="form-group">
            <label for="page_id">Page ID:</label>
            <input type="text" class="form-control" id="page_id" name="page_id" placeholder="Nhập Page ID" value="{{ old('page_id', $pageId ?? '') }}" required>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Lấy dữ liệu</button>
    </form>

    @if(isset($posts) && count($posts))
        <h4>Kết quả từ Facebook:</h4>
        @foreach($posts as $post)
            <div class="card mb-3">
                <div class="card-body">
                    <p><strong>Nội dung:</strong> {{ $post['message'] ?? '[Không có nội dung]' }}</p>
                    <p>
                        @if (!empty($post['full_picture']))
                            <img src="{{ $post['full_picture'] }}" alt="Post Image" style="max-width:100px; max-height:100px;" class="img-thumbnail">
                        @else
                            -
                        @endif
                    </p>

                    <p><strong>Ngày đăng:</strong> {{ $post['created_time'] }}</p>
                    <p><strong>Tổng cảm xúc:</strong> {{ $post['reactions']['summary']['total_count'] ?? 0 }}</p>
                    <p><strong>Bình luận:</strong> {{ $post['comments']['summary']['total_count'] ?? 0 }}</p>
                    @if(isset($post['reaction_types']))
                        <p><strong>Chi tiết cảm xúc:</strong></p>
                        <ul>
                            @foreach($post['reaction_types'] as $type => $count)
                                <li>{{ ucfirst(strtolower($type)) }}: {{ $count }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Form chuyển sang tạo chiến lược marketing -->
        <h4 class="mt-4">Tạo Chiến Lược Marketing Dựa Trên Dữ Liệu Insights</h4>
        <form action="{{ route('facebook.strategy.generate') }}" method="POST" class="mb-4">
            @csrf
            <div class="mb-3">
                <label for="topic" class="form-label">Nhập lĩnh vực (tuỳ chọn):</label>
                <input type="text" name="topic" id="topic" class="form-control" placeholder="Ví dụ: thời trang cao cấp" value="{{ old('topic', $topic ?? '') }}">
            </div>
            <!-- Ẩn dữ liệu insights để gửi sang AI -->
            <input type="hidden" name="insights_data" value="{{ json_encode($posts) }}">
            <!-- Nếu cần, chuyển luôn Page ID -->
            <input type="hidden" name="page_id" value="{{ $pageId }}">
            <button type="submit" class="btn btn-success">Tạo Chiến lược Marketing</button>
        </form>
    @endif

    @if(isset($strategy))
        <h4>Chiến Lược Marketing:</h4>
        <div class="alert alert-info">
            <p>{!! nl2br(e($strategy)) !!}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first('message') }}</div>
    @endif
</div>
@endsection