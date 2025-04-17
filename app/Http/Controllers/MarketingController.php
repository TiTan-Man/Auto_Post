<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Scenario;
use App\Models\Content;
class MarketingController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        // Lấy danh sách Scenario từ database
        $scenarios = Scenario::all();

        return view('marketing.index', compact('scenarios'));
    }

    public function generateContent(Request $request)
    {
        $request->validate([
            'scenario_id' => 'required|exists:scenarios,id',
            'description' => 'required|string|max:255',
        ]);
    
        $scenario = Scenario::findOrFail($request->input('scenario_id'));
        $description = $request->input('description');
    
        // Gọi AI để tạo nội dung
        $content = $this->aiService->generateContent($description);
    
        // Lưu nội dung vào bảng contents
        Content::create([
            'scenario_id' => $scenario->id,
            'text_content' => is_array($content) ? $content['text'] : $content,
        ]);
    
        $scenarios = Scenario::all();
    
        return view('marketing.index', compact('content', 'description', 'scenarios'));
    }
    
    
    public function generateContentWithImage(Request $request)
    {
        $request->validate([
            'scenario_id' => 'required|exists:scenarios,id',
            'description' => 'required|string|max:255',
            'image' => 'required|image|max:5120', // Thay URL ảnh bằng file ảnh, tối đa 5MB
            'page_id' => 'required|string', // ID của trang Facebook
        ]);
        
        $scenario = Scenario::findOrFail($request->input('scenario_id'));
        $description = $request->input('description');
        $pageId = $request->input('page_id');
        
        // Xử lý file ảnh đã upload
        $imagePath = $request->file('image')->getPathname();
        
        // Tạo nội dung từ AI
        $content = $this->aiService->generateContent($description);
        
        // Nếu bạn muốn lưu ảnh vào máy chủ
        $imageName = time() . '.' . $request->file('image')->extension();
        $request->file('image')->move(public_path('uploads'), $imageName);
        $savedImagePath = public_path('uploads/' . $imageName);
        
        // Lưu nội dung vào bảng contents
        Content::create([
            'scenario_id' => $scenario->id,
            'text_content' => is_array($content) ? $content['text'] : $content,
            'image_url' => asset('uploads/' . $imageName), // Lưu đường dẫn của ảnh đã lưu
        ]);
        $scenarios = Scenario::all();
        
        // Đăng bài lên Facebook kèm ảnh
        $result = $this->aiService->postToFacebookWithImage($pageId, $content, $savedImagePath);
        
        return view('marketing.index', compact('content', 'result', 'savedImagePath', 'description', 'scenarios'));
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
    
        // Lấy danh sách Scenario từ database
        $scenarios = Scenario::all();
    
        return view('marketing.index', compact('content', 'result', 'scenarios'));
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