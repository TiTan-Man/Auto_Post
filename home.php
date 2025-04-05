<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Auto Post Facebook</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <h1 class="mb-4">🚀 Tạo & Đăng Content Facebook</h1>

    <!-- Nhập chủ đề -->
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">📝 Nhập chủ đề</h5>
        <form id="content-form">
          <div class="mb-3">
            <input type="text" class="form-control" id="prompt" placeholder="Ví dụ: Công nghệ AI trong đời sống...">
          </div>
          <button type="submit" class="btn btn-primary">Tạo nội dung</button>
        </form>
      </div>
    </div>

    <!-- Danh sách content -->
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">📋 Danh sách nội dung đã tạo</h5>
        <ul id="content-list" class="list-group">
          <!-- Các bài content sẽ được thêm ở đây -->
        </ul>
      </div>
    </div>
  </div>

  <script>
    const form = document.getElementById('content-form');
    const promptInput = document.getElementById('prompt');
    const contentList = document.getElementById('content-list');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const prompt = promptInput.value.trim();
      if (!prompt) return;

      const response = await fetch('/api/generate', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ prompt })
      });

      const data = await response.json();
      const li = document.createElement('li');
      li.className = 'list-group-item d-flex justify-content-between align-items-start';
      li.innerHTML = `
        <div class="ms-2 me-auto">
          <div class="fw-bold">🔹 ${prompt}</div>
          ${data.content}
        </div>
        <button class="btn btn-success btn-sm" onclick="postToFb('${data.content.replace(/'/g, "\\'")}')">Đăng</button>
      `;
      contentList.prepend(li);
      promptInput.value = '';
    });

    async function postToFb(content) {
      const res = await fetch('/api/post', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ content })
      });

      const result = await res.json();
      if (result.success) {
        alert('✅ Đăng bài thành công!');
      } else {
        alert('❌ Lỗi khi đăng bài: ' + result.message);
      }
    }
  </script>
</body>
</html>
