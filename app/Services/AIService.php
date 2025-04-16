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

    public function generateContent(string $topic): array
    {
        $prompt = "Tạo nội dung về chủ đề: {$topic}";

        try {
            // Step 1: Generate text content using ChatGPT
            $textResponse = $this->client->post('https://api.openai.com/v1/chat/completions', [
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

            $textData = json_decode($textResponse->getBody(), true);
            $generatedText = $textData['choices'][0]['message']['content'] ?? 'Không có nội dung.';

            // Step 2: Generate image using DALL·E
            $imagePrompt = "Minh họa hình ảnh cho nội dung: {$topic}"; // Can also extract from generatedText if needed

            $imageResponse = $this->client->post('https://api.openai.com/v1/images/generations', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ],
                'json' => [
                    'prompt' => $imagePrompt,
                    'n' => 1,
                    'size' => '1024x1024'
                ]
            ]);

            $imageData = json_decode($imageResponse->getBody(), true);
            $imageUrl = $imageData['data'][0]['url'] ?? null;

            return [
                'text' => $generatedText,
                'image_url' => $imageUrl,
            ];

        } catch (\Exception $e) {
            Log::error('AI Content Generation Error: ' . $e->getMessage());
            return [
                'text' => 'Đã xảy ra lỗi khi tạo nội dung.',
                'image_url' => null,
            ];
        }
    }
}
