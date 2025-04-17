<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8" />
        <title>Tạo và Đăng Content Marketing</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" />
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
                <img src="{{ $content['image_url'] }}" alt="Hình ảnh minh họa" class="img-fluid" />
                @endif
            </div>

            <form action="{{ route('postToFacebook') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="page_id" class="form-label">Page ID Facebook</label>
                    <input type="text" name="page_id" id="page_id" class="form-control" value="{{ old('page_id') }}" />
                </div>
                <input type="hidden" name="content" value="{{ is_array($content) ? $content['text'] : $content }}" />

                <button type="submit" class="btn btn-success">Đăng lên Facebook</button>
            </form>
            @else
            <form action="{{ route('generateContent') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="scenario" class="form-label">Chọn Mô tả từ Scenario</label>
                    <select name="scenario_id" id="scenario" class="form-control" onchange="updateDescription()">
                        <option value="">-- Chọn Scenario --</option>
                        @foreach($scenarios as $scenario)
                            <option value="{{ $scenario->id }}" data-description="{{ $scenario->description }}">
                                {{ $scenario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea name="description" id="description" class="form-control" >{{ old('description') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Tạo nội dung</button>
            </form>

            @endif
            <form action="{{ route('marketing.generateWithImage') }}" method="POST" class="mb-4" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="scenario" class="form-label">Chọn Mô tả từ Scenario</label>
        <select name="scenario_id" id="scenario" class="form-control" onchange="updateDescription()">
            <option value="">-- Chọn Scenario --</option>
            @foreach($scenarios as $scenario)
                <option value="{{ $scenario->id }}" data-description="{{ $scenario->description }}">
                    {{ $scenario->name }}
                </option>
            @endforeach
        </select>
    </div>
    
    <div class="mb-3">
        <label for="description" class="form-label">Mô tả</label>
        <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
    </div>
    
    <div class="form-group">
        <label for="image">Chọn Ảnh:</label>
        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
    </div>
    
    <div class="form-group">
        <label for="page_id">Page ID:</label>
        <input type="text" class="form-control" id="page_id" name="page_id" placeholder="Nhập Page ID" required>
    </div>
    
    <button type="submit" class="btn btn-primary mt-2">Tạo và Đăng Bài</button>
</form>
            <script>
                function updateDescription() {
                    const select = document.getElementById('scenario');
                    const selectedOption = select.options[select.selectedIndex];
                    const description = selectedOption.getAttribute('data-description');
                    document.getElementById('description').value = description || '';
                }
            </script>
            @if(isset($result))
            <hr />
            <h3>Kết quả đăng bài lên Facebook</h3>
            <pre>{{ print_r($result, true) }}</pre>
            @endif
        </div>
    </body>
</html>
