<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Webinar;

class WebinarController extends Controller
{
    public function show($slug)
    {
        $webinar = Webinar::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('webinars.show', compact('webinar'));
    }
}
