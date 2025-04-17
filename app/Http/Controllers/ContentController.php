<?php
namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use App\Models\Scenario;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    public function index()
    {
        $contents = Content::with('scenario')->get(); // Lấy danh sách nội dung kèm Scenario
        return view('contents.index', compact('contents'));
    }

    public function edit($id)
    {
        $content = Content::findOrFail($id);
        $scenarios = Scenario::all(); // Lấy danh sách tất cả Scenario
        return view('contents.edit', compact('content', 'scenarios'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'scenario_id' => 'required|exists:scenarios,id',
            'text_content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $content = Content::findOrFail($id);
        
        // Xử lý upload ảnh mới
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($content->image_url && file_exists(public_path($content->image_url))) {
                unlink(public_path($content->image_url));
            }
            
            // Tạo tên file mới
            $imageName = time() . '.' . $request->file('image')->extension();
            // Lưu ảnh vào thư mục public/uploads
            $request->file('image')->move(public_path('uploads'), $imageName);
            // Lưu đường dẫn vào database
            $content->image_url = 'uploads/' . $imageName;
        }
        
        // Cập nhật các trường khác
        $content->scenario_id = $request->input('scenario_id');
        $content->text_content = $request->input('text_content');
        
        // Lưu thay đổi và ghi log
        $saved = $content->save();
        \Log::info('Cập nhật content ID: ' . $id . ', Kết quả: ' . ($saved ? 'Thành công' : 'Thất bại'));
        \Log::info('Đường dẫn ảnh sau cập nhật: ' . $content->image_url);
        
        return redirect()->route('contents.index')->with('success', 'Nội dung đã được cập nhật thành công.');
    }

    public function destroy($id)
    {
        $content = Content::findOrFail($id);
        $content->delete();

        return redirect()->route('contents.index')->with('success', 'Nội dung đã được xóa.');
    }
}