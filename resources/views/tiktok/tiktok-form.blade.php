<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Demo TikTok API - Laravel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light p-5">
<div class="container">
    <h2 class="mb-4">🎬 Đăng bài viết lên TikTok (Demo Laravel)</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('tiktok.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="caption" class="form-label">Mô tả (Caption)</label>
            <input type="text" name="caption" class="form-control" value="Đây là bài viết test gửi lên TikTok qua API Laravel." required>
        </div>

        <div class="mb-3">
            <label for="video" class="form-label">Chọn video (.mp4)</label>
            <input type="file" name="video" class="form-control" accept="video/mp4" required>
        </div>

        <button type="submit" class="btn btn-primary">🚀 Gửi bài viết lên TikTok</button>
    </form>
</div>
</body>
</html>