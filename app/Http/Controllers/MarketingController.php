<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;

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
}
