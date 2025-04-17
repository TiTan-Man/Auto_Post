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
    public function generateStrategyFromInsights(array $insights, string $topic = ''): string
    {
        // Chuyển dữ liệu insights sang chuỗi JSON được định dạng đẹp
        $insightsJson = json_encode($insights, JSON_PRETTY_PRINT);
        // dd($insightsJson);
        // Tạo prompt dựa trên dữ liệu insights
        $prompt = "Dưới đây là dữ liệu Facebook Insights đã được chuyển về dạng json về các bài viết của trang:\n\n{$insightsJson}, biên dịch lại chúng để bạn hiểu và: \n\n";
        if ($topic) {
            $prompt .= "Dựa trên dữ liệu trên, hãy tạo ra một chiến lược marketing toàn diện và xuất sắc cho việc tiếp thị sản phẩm/dịch vụ thuộc lĩnh vực \"{$topic}\". Chiến lược cần bao gồm phân tích thị trường, đối tượng mục tiêu, kênh truyền thông, nội dung quảng cáo và cách đo lường hiệu quả., nếu có nhiều loại mặt hàng trong phần dữ liệu tôi gửi lên thì chiến lược đưa ra phải chi tiết gắn với số liệu của từng mặt hàng để tôi có thể nắm bắt 1 cách chi tiết, tốt nhất là chiến lược cụ thể theo tuần";
        } else {
            $prompt .= "Dựa trên dữ liệu trên, hãy tạo ra một chiến lược marketing toàn diện và xuất sắc cho trang này. Chiến lược cần bao gồm phân tích thị trường, đối tượng mục tiêu, kênh truyền thông, nội dung quảng cáo và cách đo lường hiệu quả.";
        }

        try {
            $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ],
                'json' => [
                    'model'    => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a seasoned marketing strategist.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens' => 1500,
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['choices'][0]['message']['content'] ?? 'Không có chiến lược trả về';

        } catch (\Exception $e) {
            // Log::error('Lỗi khi gọi API OpenAI (generateStrategyFromInsights): ' . $e->getMessage());
            return 'Đã xảy ra lỗi khi tạo chiến lược marketing từ insights.'. $e->getMessage();
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
    public function postToFacebookWithImage(string $pageId, string $message, string $imagePath)
{
    $url = "https://graph.facebook.com/{$pageId}/photos";

    try {
        $response = $this->client->post($url, [
            'form_params' => [
                'caption' => $message,
                'access_token' => $this->fbAccessToken,
                'url' => $imagePath, // URL của ảnh (hoặc đường dẫn ảnh đã upload)
            ]
        ]);
        $result = json_decode($response->getBody(), true);
        return $result;
    } catch (\Exception $e) {
        Log::error('Lỗi khi đăng bài kèm ảnh lên Facebook: ' . $e->getMessage());
        return 'Đã xảy ra lỗi khi đăng bài kèm ảnh lên Facebook.' . $e->getMessage();
    }
}
}
