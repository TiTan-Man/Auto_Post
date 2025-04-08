<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\AIService;
class FacebookInsightsController extends Controller
{
    public function showInsights(Request $request)
    {
        $pageId = $request->input('page_id');
        $accessToken = env('FACEBOOK_ACCESS_TOKEN');
    
        if (!$pageId) {
            return view('facebook.insights')->with('error', 'Vui lòng nhập Page ID để xem dữ liệu.');
        }
    
        $client = new \GuzzleHttp\Client();
    
        try {
            $pageInfo = Http::get("https://graph.facebook.com/v18.0/{$pageId}", [
                'fields' => 'name,about,category,fan_count',
                'access_token' => $accessToken
            ])->json();
            // Lấy danh sách bài viết kèm lượt cảm xúc và bình luận
            $response = $client->get("https://graph.facebook.com/v22.0/{$pageId}/posts", [
                'query' => [
                    'access_token' => $accessToken,
                    'fields' => 'id,message,created_time,reactions.summary(true),comments.summary(true)',
                    'limit' => 10
                ]
            ]);
            $pageInsights = Http::get("https://graph.facebook.com/v18.0/{$pageId}/insights", [
                'metric' => 'page_views_total',
                'period' => 'day',
                'access_token' => $accessToken
            ])->json();
        
            $pageViews = $pageInsights['data'][0]['values'][0]['value'] ?? 0;
            $postsData = json_decode($response->getBody(), true);
    
            foreach ($postsData['data'] as &$post) {
                $postId = $post['id'];
            
                try {
                    // Lấy chỉ số cảm xúc
                    $reactionRes = $client->get("https://graph.facebook.com/v18.0/{$postId}/insights", [
                        'query' => [
                            'access_token' => $accessToken,
                            'metric' => 'post_reactions_by_type_total,post_impressions'
                        ]
                    ]);
            
                    $insightData = json_decode($reactionRes->getBody(), true)['data'] ?? [];
            
                    $reactionTypes = [];
                    $impressions = 0;
            
                    foreach ($insightData as $metric) {
                        if ($metric['name'] === 'post_reactions_by_type_total') {
                            $reactionTypes = $metric['values'][0]['value'] ?? [];
                        }
            
                        if ($metric['name'] === 'post_impressions') {
                            $impressions = $metric['values'][0]['value'] ?? 0;
                        }
                    }
            
                    $post['reaction_types'] = $reactionTypes;
                    $post['impressions'] = $impressions;
            
                } catch (\Exception $e) {
                    $post['reaction_types'] = ['Lỗi' => 'Không lấy được'];
                    $post['impressions'] = 0;
                }
            }
            
            
            // dd($postsData);
            return view('facebook.insights', [
                'posts' => $postsData['data'] ?? [],
                'pageViews' => $pageViews ?? 0,
                'pageId' => $pageId,
                'pageInfo' => $pageInfo ?? [],
            ]);
            
        } catch (\Exception $e) {
            return view('facebook.insights')->with('error', 'Không thể lấy dữ liệu từ Facebook.');
        }
    }


    // Hàm tích hợp OpenAI để tạo đề xuất chiến lược marketing
    public function generateStrategy(Request $request)
{
    $pageId = $request->input('page_id');
    $accessToken = env('FACEBOOK_ACCESS_TOKEN');

    if (!$pageId) {
        return response()->json(['error' => 'Page ID không hợp lệ'], 400);
    }
    
    // Giả sử chúng ta tổng hợp dữ liệu và tạo prompt cho OpenAI
    $prompt = "Trang Facebook có ID {$pageId} có lượng tương tác khá kém. ";
    
    $aiService = new \App\Services\AIService();
    try {
        $strategy = $aiService->generateMarketingStrategy($prompt);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Lỗi khi tạo đề xuất chiến lược: ' . $e->getMessage()], 500);
    }
    
    return response()->json(['strategy' => $strategy]);
}

}
