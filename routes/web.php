<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\FacebookInsightsController;
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
Route::post('/generate-content', [MarketingController::class, 'generateContent'])->name('generateContent');
Route::post('/post-to-facebook', [MarketingController::class, 'postToFacebook'])->name('postToFacebook');
Route::get('/facebook-insights', [FacebookInsightsController::class, 'showForm'])->name('facebook.insights.form');
Route::get('/facebook-insights', [FacebookInsightsController::class, 'showInsights'])->name('insights');
Route::post('/facebook-insights/strategy', [FacebookInsightsController::class, 'generateStrategy'])->name('insights.strategy');