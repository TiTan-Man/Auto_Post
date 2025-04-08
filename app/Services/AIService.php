<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $apiKey;
    protected $client;
    protected $fbAccessToken;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
        $this->client = new Client();
        $this->fbAccessToken = env('FACEBOOK_ACCESS_TOKEN');
    }

    public function generateContent(string $topic): string
    {
        $prompt = "Tạo nội dung về chủ đề: {$topic}";

        try {
            $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['choices'][0]['message']['content'] ?? 'Không có nội dung trả về';
        } catch (\Exception $e) {
            Log::error('Lỗi khi gọi API OpenAI: ' . $e->getMessage());
            return 'Đã xảy ra lỗi khi tạo nội dung.';
        }
    }

    public function postToFacebook(string $pageId, string $message)
    {
        $url = "https://graph.facebook.com/{$pageId}/feed";
        
        try {
            $response = $this->client->post($url, [
                'form_params' => [
                    'message' => $message,
                    'access_token' => $this->fbAccessToken,
                ]
            ]);
            $result = json_decode($response->getBody(), true);
            return $result;
        } catch (\Exception $e) {
            Log::error('Lỗi khi đăng bài lên Facebook: ' . $e->getMessage());
            return 'Đã xảy ra lỗi khi đăng bài lên Facebook.'. $e->getMessage();
        }
    }
}
