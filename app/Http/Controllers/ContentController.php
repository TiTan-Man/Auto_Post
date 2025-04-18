<?php
namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use App\Models\Scenario;
use Illuminate\Support\Facades\Storage;
use App\Services\AIService;
use Illuminate\Support\Facades\Log;

class ContentController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }
    public function index()
    {
        // Lấy danh sách nội dung với phân trang (10 bài viết mỗi trang)
        $contents = Content::with('scenario')->paginate(10);
    
        return view('contents.index', compact('contents'));
    }

    public function edit($id)
    {
        $content = Content::findOrFail($id);
        $scenarios = Scenario::all(); // Lấy danh sách tất cả Scenario
    
        // Lấy tên Page từ Facebook Graph API
        $pageName = null;
        if ($content->page_id) {
            $accessToken = env('FACEBOOK_ACCESS_TOKEN');
            $client = new \GuzzleHttp\Client();
    
            try {
                $response = $client->get("https://graph.facebook.com/v12.0/{$content->page_id}", [
                    'query' => [
                        'access_token' => $accessToken,
                        'fields' => 'name',
                    ],
                ]);
                $pageData = json_decode($response->getBody(), true);
                $pageName = $pageData['name'] ?? null;
            } catch (\Exception $e) {
                Log::error('Lỗi khi lấy tên Page: ' . $e->getMessage());
            }
        }
    
        return view('contents.edit', compact('content', 'scenarios', 'pageName'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'scenario_id' => 'required|exists:scenarios,id',
            'text_content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $content = Content::findOrFail($id);
    
    
        // Cập nhật nội dung
        $content->scenario_id = $request->input('scenario_id');
        $content->text_content = $request->input('text_content');
    
        $saved = $content->save();
    
        // 👉 Cập nhật bài viết Facebook (nếu có facebook_post_id)
        if ($content->facebook_post_id) {
            $result = $this->aiService->updateFacebookPost(
                $content->facebook_post_id,
                $content->text_content
            );
            Log::info('Đã cập nhật bài viết Facebook:', $result ?? []);
        }
    
        return redirect()->route('contents.index')->with('success', 'Nội dung đã được cập nhật và đồng bộ với Facebook.');
    }

    public function destroy($id)
    {
        $content = Content::findOrFail($id);
        $content->delete();

        return redirect()->route('contents.index')->with('success', 'Nội dung đã được xóa.');
    }
}