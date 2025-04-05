<?php
use Dotenv\Dotenv;

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$apiKey = $_ENV['OPENAI_API_KEY'];

$fine_tune_id = "ftjob-FJV85TtNi9ef65upyOW1F3Cl"; // Thay bằng Job ID của bạn

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/fine_tuning/jobs/$fine_tune_id/events");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $api_key"
]);

$response = curl_exec($ch);
curl_close($ch);

echo "<pre>$response</pre>";
?>
