<?php
use Dotenv\Dotenv;

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$apiKey = $_ENV['OPENAI_API_KEY'];

$file_id = "file-YTuRDyCFHyK5tJTPLkVYNy"; // Thay bằng ID của file bạn vừa tải lên

// Gửi yêu cầu tạo Fine-tuning job
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/fine_tuning/jobs");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $api_key",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "training_file" => $file_id,
    "model" => "gpt-3.5-turbo"
]));

$response = curl_exec($ch);
curl_close($ch);

// Hiển thị phản hồi
echo "<pre>$response</pre>";
?>
