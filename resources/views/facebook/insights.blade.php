@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Facebook Page Insights</h1>

    <form method="GET" action="{{ route('facebook.insights') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="page_id" class="form-control" placeholder="Nhập Page ID" value="{{ request('page_id') }}">
            <button type="submit" class="btn btn-primary">Lấy dữ liệu</button>
        </div>
    </form>

    @if(isset($error))
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    @if(isset($pageInfo['name']))
        <h2>Thông tin Page</h2>
        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Tên:</strong> {{ $pageInfo['name'] }}</li>
            <li class="list-group-item"><strong>Loại:</strong> {{ $pageInfo['category'] ?? 'Không rõ' }}</li>
            <li class="list-group-item"><strong>Lượt thích:</strong> {{ number_format($pageInfo['fan_count']) }}</li>
            <li class="list-group-item"><strong>Giới thiệu:</strong> {{ $pageInfo['about'] ?? 'Không có mô tả' }}</li>
        </ul>
    @endif

    @if(!empty($insights))
        <h2>Thống kê tương tác Page (Daily)</h2>
        <table class="table table-bordered mb-4">
            <thead>
                <tr>
                    <th>Chỉ số</th>
                    <th>Giá trị gần nhất</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($insights as $insight)
                    <tr>
                        <td>{{ $insight['title'] }}</td>
                        <td>{{ $insight['values'][0]['value'] ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(!empty($posts))
        <h2>10 Bài Viết Gần Đây</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Nội dung</th>
                    <th>Hiển thị</th>
                    <th>Người tương tác</th>
                    <th>Click</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($posts as $post)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($post['created_time'])->format('d/m/Y') }}</td>
                        <td>{{ Str::limit($post['message'] ?? '(Không có nội dung)', 60) }}</td>
                        <td>{{ $post['insights']['data'][0]['values'][0]['value'] ?? '-' }}</td>
                        <td>{{ $post['insights']['data'][1]['values'][0]['value'] ?? '-' }}</td>
                        <td>{{ $post['insights']['data'][2]['values'][0]['value'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(isset($pageInfo['name']))
    <div class="mb-4">
        <button type="submit" id="generateStrategy" class="btn btn-success">Đề xuất chiến lược marketing</button>
    </div>
    <div id="strategyResult" class="alert alert-info" style="display:none;"></div>
    @endif
</div>

@endsection

@section('scripts')
<script>
    document.getElementById('generateStrategy').addEventListener('click', function() {
        let pageId = '{{ $pageId ?? "" }}';
    fetch("{{ route('insights.strategy') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ page_id: pageId })
    })
    .then(res => res.json())
    .then(data => {
        if(data.strategy) {
            document.getElementById('strategyResult').style.display = 'block';
            document.getElementById('strategyResult').innerText = data.strategy;
        } else if(data.error) {
            document.getElementById('strategyResult').style.display = 'block';
            document.getElementById('strategyResult').innerText = data.error;
        }
    })
    .catch(err => {
        document.getElementById('strategyResult').style.display = 'block';
        document.getElementById('strategyResult').innerText = 'Có lỗi xảy ra: ' + err.message;
    });
});

</script>
@endsection
