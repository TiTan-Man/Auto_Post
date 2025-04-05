<?php
use GuzzleHttp\Client;
use Dotenv\Dotenv;

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['OPENAI_API_KEY'];
header("Content-Type: application/json");


$input = json_decode(file_get_contents("php://input"), true);
$user_message = $input['message'];
//echo "gsfgsfgsfg". $user_message;

$url = "https://api.openai.com/v1/chat/completions";
$data = [
    "model" => "ft:gpt-3.5-turbo-0125:newway::BFjbtvnD",//"gpt-3.5-turbo",
    "messages" => [
        ["role" => "system", "content" => "Bạn là trợ lý hỗ trợ khách hàng."],
        ["role" => "user", "content" => $user_message]
    ],
    "temperature" => 0.7,
    "max_tokens" => 50	
];


$headers = [
    "Content-Type: application/json",
    "Authorization: Bearer $api_key"
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$bot_reply = $result['choices'][0]['message']['content'] ?? "Xin lỗi, tôi chưa hiểu câu hỏi của bạn.".$response;

echo json_encode(["reply" => $bot_reply]);
?>
