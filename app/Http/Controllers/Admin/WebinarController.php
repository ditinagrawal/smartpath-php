<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class WebinarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $webinars = Webinar::latest()->paginate(10);
        return view('admin.webinars.index', compact('webinars'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.webinars.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'event_date' => 'required|date',
            'event_time' => 'nullable|max:100',
            'location' => 'nullable|max:255',
            'registration_link' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $imageDir = public_path('images/webinars');
            
            // Create directory if it doesn't exist
            if (!File::exists($imageDir)) {
                File::makeDirectory($imageDir, 0755, true);
            }
            
            $image->move($imageDir, $imageName);
            $imagePath = 'images/webinars/' . $imageName;
        }

        Webinar::create([
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')),
            'excerpt' => $request->input('excerpt'),
            'content' => $request->input('content'),
            'event_date' => $request->input('event_date'),
            'event_time' => $request->input('event_time'),
            'location' => $request->input('location'),
            'registration_link' => $request->input('registration_link'),
            'is_published' => $request->has('is_published'),
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.webinars.index')->with('success', 'Webinar created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $webinar = Webinar::findOrFail($id);
        return view('admin.webinars.show', compact('webinar'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $webinar = Webinar::findOrFail($id);
        return view('admin.webinars.edit', compact('webinar'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'event_date' => 'required|date',
            'event_time' => 'nullable|max:100',
            'location' => 'nullable|max:255',
            'registration_link' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $webinar = Webinar::findOrFail($id);
        
        $imagePath = $webinar->image;
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($webinar->image && File::exists(public_path($webinar->image))) {
                File::delete(public_path($webinar->image));
            }
            
            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $imageDir = public_path('images/webinars');
            
            // Create directory if it doesn't exist
            if (!File::exists($imageDir)) {
                File::makeDirectory($imageDir, 0755, true);
            }
            
            $image->move($imageDir, $imageName);
            $imagePath = 'images/webinars/' . $imageName;
        }
        
        // Handle image removal
        if ($request->has('remove_image') && $request->remove_image) {
            if ($webinar->image && File::exists(public_path($webinar->image))) {
                File::delete(public_path($webinar->image));
            }
            $imagePath = null;
        }
        
        $webinar->update([
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')),
            'excerpt' => $request->input('excerpt'),
            'content' => $request->input('content'),
            'event_date' => $request->input('event_date'),
            'event_time' => $request->input('event_time'),
            'location' => $request->input('location'),
            'registration_link' => $request->input('registration_link'),
            'is_published' => $request->has('is_published'),
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.webinars.index')->with('success', 'Webinar updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $webinar = Webinar::findOrFail($id);
        
        // Delete image if exists
        if ($webinar->image && File::exists(public_path($webinar->image))) {
            File::delete(public_path($webinar->image));
        }
        
        $webinar->delete();

        return redirect()->route('admin.webinars.index')->with('success', 'Webinar deleted successfully!');
    }
}
