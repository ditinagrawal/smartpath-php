<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Category;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with('category')
            ->where('is_published', true)
            ->latest()
            ->paginate(10);

        return view('blogs.index', compact('blogs'));
    }

    public function show($slug)
    {
        $blog = Blog::with('category')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('blogs.show', compact('blog'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $blogs = Blog::with('category')
            ->where('category_id', $category->id)
            ->where('is_published', true)
            ->latest()
            ->get();

        // Get all categories for sidebar
        $categories = Category::withCount(['blogs' => function($query) {
            $query->where('is_published', true);
        }])
        ->orderBy('name')
        ->get();

        return view('blogs.category', compact('category', 'blogs', 'categories'));
    }
}
