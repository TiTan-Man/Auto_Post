<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use Dotenv\Dotenv;

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['OPENAI_API_KEY'];

$client = new Client();
$response = $client->post('https://api.openai.com/v1/chat/completions', [
    'headers' => [
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $apiKey,
    ],
    'json' => [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'You are a helpful assistant.'],
            ['role' => 'user', 'content' => 'Tạo nội dung về chủ đề "Công nghệ AI trong giáo dục"']
        ],
    ]
]);

$body = $response->getBody();
$data = json_decode($body, true);
$content = $data['choices'][0]['message']['content'];

// Lưu nội dung vào file local
$filePath = 'content.txt';
$file = fopen($filePath, 'w');
if ($file) {
    fwrite($file, $content);
    fclose($file);
    echo "Nội dung đã được lưu vào file: " . $filePath;
} else {
    echo "Không thể mở file để lưu nội dung.";
}
?>
