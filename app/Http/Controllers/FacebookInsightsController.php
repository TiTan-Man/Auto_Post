<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FacebookInsightsController extends Controller
{
    public function showInsights(Request $request)
    {
        $pageId = $request->input('page_id');
        $accessToken = env('FACEBOOK_ACCESS_TOKEN');

        if (!$pageId) {
            return view('insights')->with('error', 'Vui lòng nhập Page ID để xem dữ liệu.');
        }

        try {
            // 1. Lấy thông tin cơ bản về Page
            $pageInfo = Http::get("https://graph.facebook.com/v19.0/{$pageId}", [
                'fields' => 'name,fan_count,about,category',
                'access_token' => $accessToken
            ])->json();

            // 2. Lấy Insights của Page
            $metrics = [
                'page_impressions',
                'page_engaged_users',
                'page_views_total',
                'page_post_engagements',
                'page_consumptions',
            ];
            $insightsResponse = Http::get("https://graph.facebook.com/v19.0/{$pageId}/insights", [
                'metric' => implode(',', $metrics),
                'period' => 'day',
                'access_token' => $accessToken
            ]);

            $insights = $insightsResponse->json()['data'] ?? [];

            // 3. Lấy danh sách bài viết gần đây với insights
            $postsResponse = Http::get("https://graph.facebook.com/v19.0/{$pageId}/posts", [
                'fields' => 'message,created_time,insights.metric(post_impressions,post_engaged_users,post_clicks)',
                'limit' => 10,
                'access_token' => $accessToken
            ]);
            $posts = $postsResponse->json()['data'] ?? [];
        } catch (\Exception $e) {
            return view('insights')->with('error', 'Có lỗi xảy ra khi lấy dữ liệu: ' . $e->getMessage());
        }

        return view('insights', [
            'pageInfo' => $pageInfo,
            'insights' => $insights,
            'posts' => $posts,
            'pageId' => $pageId
        ]);
    }

    // Hàm tích hợp OpenAI để tạo đề xuất chiến lược marketing
    public function generateStrategy(Request $request)
    {
        $pageId = $request->input('page_id');
        $accessToken = env('FB_PAGE_ACCESS_TOKEN');

        if (!$pageId) {
            return response()->json(['error' => 'Page ID không hợp lệ'], 400);
        }

        // Giả sử ta tổng hợp dữ liệu insights và post để tạo prompt
        // (Có thể tổng hợp một vài dữ liệu tiêu biểu, ví dụ: tổng số page_engaged_users trong 1 tháng)
        try {
            $dataResponse = Http::get("https://graph.facebook.com/v19.0/{$pageId}/insights", [
                'metric' => 'page_engaged_users',
                'period' => 'day',
                'access_token' => $accessToken
            ]);
            $dataInsights = $dataResponse->json()['data'] ?? [];
            
            // Giả sử chúng ta tính tổng số engaged users của ngày cuối cùng (hoặc tổng trung bình)
            $totalEngaged = 0;
            $count = 0;
            foreach ($dataInsights as $metric) {
                foreach ($metric['values'] as $value) {
                    if (isset($value['value']) && is_numeric($value['value'])) {
                        $totalEngaged += $value['value'];
                        $count++;
                    }
                }
            }
            $averageEngaged = $count > 0 ? round($totalEngaged / $count) : 0;

            // Tạo prompt cho OpenAI
            $prompt = "Trang Facebook có ID {$pageId} có trung bình {$averageEngaged} người tương tác mỗi ngày. Dựa trên thông tin này, đề xuất một chiến lược marketing để tăng tương tác trên trang.";
            
            // Gọi API của OpenAI - ví dụ sử dụng endpoint chat completions với GPT-3.5 hoặc GPT-4
            $openAiApiKey = env('OPENAI_API_KEY');
            $openAiResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $openAiApiKey,
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo', // hoặc GPT-4 nếu bạn có quyền truy cập
                'messages' => [
                    ['role' => 'system', 'content' => 'Bạn là chuyên gia marketing.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 200,
            ]);
            $openAiData = $openAiResponse->json();
            $strategy = $openAiData['choices'][0]['message']['content'] ?? 'Không có đề xuất nào.';

        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi tạo đề xuất chiến lược: ' . $e->getMessage()], 500);
        }

        return response()->json(['strategy' => $strategy]);
    }
}
