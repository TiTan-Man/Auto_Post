<?php

namespace App\Http\Controllers;

use App\Models\Scenario;
use Illuminate\Http\Request;

class ScenarioController extends Controller
{
    // Hiển thị danh sách
    public function index()
    {
        $scenarios = Scenario::all();
        return view('scenarios.index', compact('scenarios'));
    }

    // Hiển thị form tạo mới
    public function create()
    {
        return view('scenarios.create');
    }

    // Lưu dữ liệu mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        Scenario::create($request->all());
        return redirect()->route('scenarios.index')->with('success', 'Scenario created successfully.');
    }

    // Hiển thị chi tiết
    public function show(Scenario $scenario)
    {
        return view('scenarios.show', compact('scenario'));
    }

    // Hiển thị form chỉnh sửa
    public function edit(Scenario $scenario)
    {
        return view('scenarios.edit', compact('scenario'));
    }

    // Cập nhật dữ liệu
    public function update(Request $request, Scenario $scenario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $scenario->update($request->all());
        return redirect()->route('scenarios.index')->with('success', 'Scenario updated successfully.');
    }

    // Xóa dữ liệu
    public function destroy(Scenario $scenario)
    {
        $scenario->delete();
        return redirect()->route('scenarios.index')->with('success', 'Scenario deleted successfully.');
    }
}