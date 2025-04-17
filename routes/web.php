<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\FacebookInsightsController;
use App\Http\Controllers\ScenarioController;
use App\Http\Controllers\ContentController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('marketing.index');
// });
Route::get('/marketing', [MarketingController::class, 'index']);
Route::post('/marketing/generate-with-image', [MarketingController::class, 'generateContentWithImage'])->name('marketing.generateWithImage');
Route::post('/generate-content', [MarketingController::class, 'generateContent'])->name('generateContent');
Route::post('/post-to-facebook', [MarketingController::class, 'postToFacebook'])->name('postToFacebook');

// Route cho Facebook Insights
Route::get('/facebook-insights', [FacebookInsightsController::class, 'showForm'])->name('facebook.insights.form');
Route::post('/facebook-insights', [FacebookInsightsController::class, 'getInsights'])->name('facebook.insights');

// Route để tạo chiến lược từ dữ liệu insights
Route::post('/facebook-strategy', [FacebookInsightsController::class, 'generateStrategyFromInsights'])->name('facebook.strategy.generate');
Route::post('/weather', [MarketingController::class, 'getWeather']);
Route::view('/tiktok', 'tiktok.tiktok-form');
Route::post('/tiktok/upload', function (Request $request) {
    // Chỉ là mô phỏng gửi API
    // Log::info('📦 Dữ liệu gửi lên TikTok:', $request->all());

    return redirect('/tiktok')->with('success', '✅ Bài viết đã được gửi (giả lập) thành công lên TikTok!');
})->name('tiktok.upload');

Route::resource('scenarios', ScenarioController::class);

Route::get('/contents', [ContentController::class, 'index'])->name('contents.index'); // Xem danh sách
Route::get('/contents/{id}/edit', [ContentController::class, 'edit'])->name('contents.edit'); // Sửa
Route::put('/contents/{id}', [ContentController::class, 'update'])->name('contents.update'); // Cập nhật
Route::delete('/contents/{id}', [ContentController::class, 'destroy'])->name('contents.destroy'); // Xóa