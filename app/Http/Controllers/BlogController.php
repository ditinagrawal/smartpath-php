<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;

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
}
