<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class FacebookInsightsController extends Controller
{
    public function showForm()
    {
        return view('facebook.insights');
    }

    public function getInsights(Request $request)
    {
        $request->validate([
            'page_id' => 'required|string',
        ]);

        $accessToken = env('FACEBOOK_ACCESS_TOKEN');
        $pageId = $request->input('page_id');

        $client = new Client();

        try {
            // Lấy danh sách bài viết kèm lượt cảm xúc và bình luận
            $response = $client->get("https://graph.facebook.com/v22.0/{$pageId}/posts", [
                'query' => [
                    'access_token' => $accessToken,
                    'fields' => 'id,message,created_time,reactions.summary(true),comments.summary(true)',
                    'limit' => 10
                ]
            ]);

            $postsData = json_decode($response->getBody(), true);
            foreach ($postsData['data'] as &$post) {
                $postId = $post['id'];
            
                try {
                    // Lấy số lượng từng loại cảm xúc cho mỗi post
                    $reactionRes = $client->get("https://graph.facebook.com/v18.0/{$postId}/insights", [
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
                'posts' => $postsData['data'] ?? [],
                'pageId' => $pageId,
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook API error: ' . $e->getMessage());

            return 'Lỗi khi lấy dữ liệu từ Facebook.'. $e->getMessage();
        }
    }
}
