<?php
use Dotenv\Dotenv;

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$apiKey = $_ENV['OPENAI_API_KEY'];

$file_path = "dataset.jsonl"; // Đường dẫn file JSONL

// Kiểm tra file tồn tại
if (!file_exists($file_path)) {
    die("Lỗi: File không tồn tại.");
}

// Gửi yêu cầu tải lên file
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/files");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $api_key",
    "Content-Type: multipart/form-data"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    "purpose" => "fine-tune",
    "file" => new CURLFile($file_path)
]);

$response = curl_exec($ch);
curl_close($ch);

// Hiển thị phản hồi từ OpenAI
echo "<pre>$response</pre>";
?>
