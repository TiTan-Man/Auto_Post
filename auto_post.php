<?php
require 'vendor/autoload.php';
use GuzzleHttp\Client;
use Dotenv\Dotenv;

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();



function generateContent($prompt) {
$apiKey = $_ENV['OPENAI_API_KEY'];

    $client = new Client();

    try {
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ],
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 250,
            ]
        ]);
    } catch (Exception $e) {
        die("Lỗi khi gọi OpenAI API: " . $e->getMessage());
    }

    $data = json_decode($response->getBody(), true);
    return trim($data['choices'][0]['message']['content']);
}

function postToFacebook($pageId, $message, $accessToken) {
    $client = new Client();
    $url = "https://graph.facebook.com/{$pageId}/feed";

    try {
        $response = $client->post($url, [
            'form_params' => [ // ✅ Thay vì 'query'
                'message' => $message,
                'access_token' => $accessToken,
            ]
        ]);
    } catch (Exception $e) {
        die("Lỗi khi đăng bài lên Facebook: " . $e->getMessage());
    }
    
    $result = json_decode($response->getBody(), true);
    return $result;
}


// Sử dụng biến môi trường để lưu thông tin bảo mật
$prompt = "Viết một bài đăng Facebook thật thu hút về chủ đề công nghệ mới.";
$content = generateContent($prompt);

$pageId = "581712621700730";
$accessToken = $_ENV['FACEBOOK_ACCESS_TOKEN'];;

$result = postToFacebook($pageId, $content, $accessToken);
echo "Kết quả đăng bài:\n";
print_r($result);
?>
