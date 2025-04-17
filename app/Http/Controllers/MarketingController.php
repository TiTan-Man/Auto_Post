<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class MarketingController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        return view('marketing.index');
    }

    public function generateContent(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
        ]);

        $topic = $request->input('topic');
        $content = $this->aiService->generateContent($topic);

        return view('marketing.index', compact('content', 'topic'));
    }
    public function generateContentWithImage(Request $request)
{
    $request->validate([
        'topic' => 'required|string|max:255',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Kiểm tra file ảnh
        'page_id' => 'required|string', // ID của trang Facebook
    ]);

    $topic = $request->input('topic');
    $pageId = $request->input('page_id');

    // Lưu ảnh vào thư mục tạm thời
    $imagePath = $request->file('image')->store('uploads', 'public');

    // Tạo nội dung từ AI
    $content = $this->aiService->generateContent($topic);

    // Đăng bài lên Facebook kèm ảnh
    $imageUrl = asset('storage/' . $imagePath); // URL công khai của ảnh
    $result = $this->aiService->postToFacebookWithImage($pageId, $content, $imageUrl);

    return view('marketing.index', compact('content', 'result', 'imageUrl', 'topic'));
}
    public function postToFacebook(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'page_id' => 'required|string',
        ]);

        $content = $request->input('content');
        $pageId = $request->input('page_id');
        $result = $this->aiService->postToFacebook($pageId, $content);

        return view('marketing.index', compact('content', 'result'));
    }
    public function getWeather(Request $request)
    {
        $location = $request->input('location');
        $date = $request->input('date'); // Chưa dùng vì API miễn phí không hỗ trợ dự báo theo ngày cụ thể

        $apiKey = env('OPENWEATHER_API_KEY');

        $response = Http::get("http://api.openweathermap.org/data/2.5/weather", [
            'q' => $location,
            'appid' => $apiKey,
            'units' => 'metric',
            'lang' => 'vi'
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Không lấy được dữ liệu thời tiết'], 500);
        }

        $data = $response->json();

        return response()->json([
            'location'    => $location,
            'date'        => now()->toDateString(),
            'temperature' => $data['main']['temp'] . '°C',
            'condition'   => $data['weather'][0]['description']
        ]);
    }
}
