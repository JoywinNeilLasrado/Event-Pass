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
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories');
        }

        Category::create([
            'name' => $data['name'], 
            'slug' => Str::slug($data['name']),
            'image_path' => $imagePath
        ]);
        return back()->with('success', 'Category created.');
    }
    
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }
    
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);
        
        if ($request->hasFile('image')) {
            if ($category->image_path) {
                \Illuminate\Support\Facades\Storage::delete($category->image_path);
            }
            $category->image_path = $request->file('image')->store('categories');
        }

        $category->name = $data['name'];
        $category->slug = Str::slug($data['name']);
        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->image_path) {
            \Illuminate\Support\Facades\Storage::delete($category->image_path);
        }
        $category->delete();
        return back()->with('success', "Category \"{$category->name}\" deleted.");
    }
}
