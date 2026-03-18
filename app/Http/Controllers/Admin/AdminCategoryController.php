<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('events')->latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100|unique:categories,name']);
        Category::create(['name' => $data['name'], 'slug' => Str::slug($data['name'])]);
        return back()->with('success', 'Category created.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', "Category \"{$category->name}\" deleted.");
    }
}
