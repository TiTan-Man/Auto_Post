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
        <form action="{{ route('contents.update', $content->id) }}" method="POST">
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

            <!-- Hiển thị tên Page -->
            <div class="mb-3 d-flex align-items-center">
                <label class="form-label me-2">Tên Page:</label>
                <p class="form-control-plaintext mb-0">
                    {{ $pageName ?? 'Không có thông tin Page' }}
                </p>
            </div>

            <!-- Hiển thị hình ảnh hiện tại -->
            <div class="mb-3">
                <label class="form-label">Ảnh hiện tại</label>
                @if($content->image_url)
                    <div class="border p-2 mb-2">
                        <img src="{{ asset($content->image_url) }}?t={{ time() }}" alt="Ảnh hiện tại" class="img-fluid" style="max-height: 200px;">
                    </div>
                @else
                    <p class="text-muted">Không có ảnh</p>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('contents.index') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</body>
</html>