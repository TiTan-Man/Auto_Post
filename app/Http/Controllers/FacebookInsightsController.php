<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Services\AIService;

class FacebookInsightsController extends Controller
{
    public function showForm()
    {
        return view('facebook.insights');
    }

    // Lấy dữ liệu Facebook Insights (danh sách bài viết kèm lượt tương tác)
    public function getInsights(Request $request)
    {
        $request->validate([
            'page_id' => 'required|string',
        ]);

        $accessToken = env('FACEBOOK_ACCESS_TOKEN');
        $pageId = $request->input('page_id');

        $client = new Client();

        try {
            // Lấy danh sách bài viết kèm thông tin tương tác (bao gồm cảm xúc và bình luận)
            $response = $client->get("https://graph.facebook.com/v22.0/{$pageId}/posts", [
                'query' => [
                    'access_token' => $accessToken,
                    'fields' => 'id,message,created_time,reactions.summary(true),comments.summary(true)',
                    'limit' => 10
                ]
            ]);

            $postsData = json_decode($response->getBody(), true);

            // Với mỗi bài, nếu muốn lấy chi tiết cảm xúc theo loại:
            foreach ($postsData['data'] as &$post) {
                $postId = $post['id'];

                try {
                    $reactionRes = $client->get("https://graph.facebook.com/v22.0/{$postId}/insights", [
                        'query' => [
                            'access_token' => $accessToken,
                            'metric' => 'post_reactions_by_type_total'
                        ]
                    ]);
                    $reactionData = json_decode($reactionRes->getBody(), true);
                    $post['reaction_types'] = $reactionData['data'][0]['values'][0]['value'] ?? [];
                } catch (\Exception $e) {
                    $post['reaction_types'] = ['Lỗi' => 'Không lấy được'];
                }
            }

            return view('facebook.insights', [
                'posts'  => $postsData['data'] ?? [],
                'pageId' => $pageId,
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook API error: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Không thể lấy dữ liệu từ Facebook. Vui lòng kiểm tra Page ID và Access Token.']);
        }
    }

    // Tạo chiến lược marketing từ dữ liệu insights đã lấy
    public function generateStrategyFromInsights(Request $request)
{
    $request->validate([
        'insights_data' => 'required|string',
    ]);

    // Nhận dữ liệu insights dưới dạng JSON và giải mã
    $insightsDataJson = $request->input('insights_data');
    $insightsData = json_decode($insightsDataJson, true);

    // Ép kiểu để đảm bảo $topic luôn là string
    $topic = $request->input('topic') ?: '';

    // Gọi AIService để tạo chiến lược marketing từ dữ liệu insights
    $strategy = app(AIService::class)->generateStrategyFromInsights($insightsData, $topic);

    return view('facebook.insights', [
        'posts'    => $insightsData,
        'strategy' => $strategy,
        'pageId'   => $request->input('page_id', ''),
        'topic'    => $topic,
    ]);
}
}
