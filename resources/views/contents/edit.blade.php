<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Nội dung</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Sửa Nội dung</h1>
        <form action="{{ route('contents.update', $content->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Dropdown chọn Scenario -->
            <div class="mb-3">
                <label for="scenario_id" class="form-label">Chọn Scenario</label>
                <select name="scenario_id" id="scenario_id" class="form-control">
                    <option value="">-- Chọn Scenario --</option>
                    @foreach($scenarios as $scenario)
                        <option value="{{ $scenario->id }}" {{ $content->scenario_id == $scenario->id ? 'selected' : '' }}>
                            {{ $scenario->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Textarea chỉnh sửa nội dung -->
            <div class="mb-3">
                <label for="text_content" class="form-label">Nội dung</label>
                <textarea name="text_content" id="text_content" class="form-control" rows="5">{{ $content->text_content }}</textarea>
            </div>

            <!-- Input chỉnh sửa ảnh -->
            <div class="mb-3">
                <label for="image" class="form-label">Ảnh</label>
                <input type="file" name="image" id="image" class="form-control">
                @if($content->image_url)
                    <div class="mt-2">
                        <p>Ảnh hiện tại:</p>
                        <div class="border p-2 mb-2">
                            <!-- Đường dẫn đến thư mục public/uploads -->
                            <img src="{{ asset($content->image_url) }}?t={{ time() }}" alt="Ảnh hiện tại" class="img-fluid" style="max-height: 200px;">
                        </div>
                    </div>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('contents.index') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</body>
</html>