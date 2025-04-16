<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo và Đăng Content Marketing</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Tạo và Đăng Content Marketing</h1>

        @if(isset($content))
                <div class="alert alert-info">
                    <h3>Nội dung tạo ra:</h3>
                    <p>{{ is_array($content) ? $content['text'] : $content }}</p>

@if(is_array($content) && isset($content['image_url']))
    <h4>Hình ảnh minh họa:</h4>
    <img src="{{ $content['image_url'] }}" alt="Hình ảnh minh họa" class="img-fluid">
@endif

                </div>

                <form action="{{ route('postToFacebook') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="page_id" class="form-label">Page ID Facebook</label>
                        <input type="text" name="page_id" id="page_id" class="form-control" value="{{ old('page_id') }}">
                    </div>
                    <input type="hidden" name="content" value="{{ is_array($content) ? $content['text'] : $content }}">

                    <button type="submit" class="btn btn-success">Đăng lên Facebook</button>
                </form>
            @else
        <form action="{{ route('generateContent') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="topic" class="form-label">Chủ đề</label>
                <input type="text" name="topic" id="topic" class="form-control" value="{{ old('topic') }}">
            </div>
            <button type="submit" class="btn btn-primary">Tạo nội dung</button>
        </form>
        
        @endif

        @if(isset($result))
            <hr>
            <h3>Kết quả đăng bài lên Facebook</h3>
            <pre>{{ print_r($result, true) }}</pre>
        @endif
    </div>
</body>
</html>
