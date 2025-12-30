<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\WebinarController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\WebinarController as AdminWebinarController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Blog;
use App\Models\Webinar;

// Helper function to serve HTML files with corrected asset paths
if (!function_exists('serveSmartPathHtml')) {
    function serveSmartPathHtml($htmlFile) {
        $filePath = public_path('smartpath/' . $htmlFile);
        
        if (!file_exists($filePath)) {
            \Log::error("SmartPath HTML file not found: {$filePath}");
            abort(404, "File not found: {$htmlFile}");
        }
        
        $content = file_get_contents($filePath);
        
        if ($content === false) {
            abort(500, "Could not read file: {$htmlFile}");
        }
        
        // Fix relative asset paths to use /smartpath/ prefix
        $content = str_replace('href="assets/', 'href="/smartpath/assets/', $content);
        $content = str_replace("href='assets/", "href='/smartpath/assets/", $content);
        $content = str_replace('src="assets/', 'src="/smartpath/assets/', $content);
        $content = str_replace("src='assets/", "src='/smartpath/assets/", $content);
        
        // Fix data attributes that contain asset paths (like data-background for banner images)
        $content = str_replace('data-background="assets/', 'data-background="/smartpath/assets/', $content);
        $content = str_replace("data-background='assets/", "data-background='/smartpath/assets/", $content);
        $content = str_replace('data-bg="assets/', 'data-bg="/smartpath/assets/', $content);
        $content = str_replace("data-bg='assets/", "data-bg='/smartpath/assets/", $content);
        
        // Fix any other data attributes that might contain asset paths
        $content = preg_replace('/(data-[^=]+)=["\']assets\//', '$1="/smartpath/assets/', $content);
        
        // Fix inline style backgrounds (handle both single and double quotes)
        $content = preg_replace('/url\(["\']?assets\//', 'url("/smartpath/assets/', $content);
        
        // Fix internal HTML page links
        // Map specific pages to their routes
        $pageMap = [
            'index.html' => '/',
            'about-us.html' => '/about',
            'contact.html' => '/contact',
        ];
        
        foreach ($pageMap as $oldLink => $newLink) {
            $content = str_replace('href="' . $oldLink, 'href="' . $newLink, $content);
            $content = str_replace("href='" . $oldLink, "href='" . $newLink, $content);
        }
        
        // Fix other HTML links (remove .html extension for catch-all route)
        // Only match href attributes, not src (to avoid affecting images/CSS/JS)
        $content = preg_replace('/href=["\']([^"\']+)\.html([^"\']*)["\']/', 'href="$1$2"', $content);

        // Inject dynamic latest news blogs into the homepage
        if ($htmlFile === 'index.html') {
            try {
                // Check if categories table exists
                if (Schema::hasTable('categories')) {
                    $newsBlogs = Blog::with('category')
                        ->where('is_published', true)
                        ->whereHas('category', function($query) {
                            $query->where('name', 'News');
                        })
                        ->latest()
                        ->take(3)
                        ->get();
                } else {
                    // Fallback: get blogs with category column (old way) or just latest blogs
                    $newsBlogs = Blog::where('is_published', true)
                        ->latest()
                        ->take(3)
                        ->get();
                }
            } catch (\Exception $e) {
                // Fallback if there's any error
                $newsBlogs = Blog::where('is_published', true)
                    ->latest()
                    ->take(3)
                    ->get();
            }

            $cardsHtml = '';

            foreach ($newsBlogs as $blog) {
                $imageUrl = $blog->image_url ?? '/smartpath/assets/img/blog/bg-1.jpg';
                $date = $blog->created_at?->format('d M Y') ?? '';
                $blogUrl = url('/news/' . $blog->slug);

                $cardsHtml .= '
            <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
              <div class="it-blog-item-box" data-background="assets/img/blog/bg-1.jpg">
                <div class="it-blog-item">
                  <div class="it-blog-thumb fix">
                    <a href="' . e($blogUrl) . '">
                      <img src="' . e($imageUrl) . '" alt="' . e($blog->title) . '" />
                    </a>
                  </div>
                  <div class="it-blog-meta pb-15">
                    <span>
                      <i class="fa-solid fa-calendar-days"></i>
                      ' . e($date) . '
                    </span>
                    <span>
                      <i class="fa-light fa-messages"></i>
                      ' . e(is_object($blog->category) ? ($blog->category->name ?? 'News') : ($blog->category ?? 'News')) . '
                    </span>
                  </div>
                  <h4 class="it-blog-title">
                    <a href="' . e($blogUrl) . '">
                      ' . e($blog->title) . '
                    </a>
                  </h4>
                  <a class="it-btn sm" href="' . e($blogUrl) . '">
                    <span>
                      Read More
                      <svg width="17" height="14" viewBox="0 0 17 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                    </span>
                  </a>
                </div>
              </div>
            </div>';
            }

            // If no news blogs, leave the section empty (or you could add a fallback message)
            $newsSectionHtml = '<!-- NEWS_SECTION_START -->
          <div class="row">
' . $cardsHtml . '
          </div>
          <!-- NEWS_SECTION_END -->';

            $content = preg_replace(
                '/<!-- NEWS_SECTION_START -->(.*?)<!-- NEWS_SECTION_END -->/s',
                $newsSectionHtml,
                $content
            );
        }
        
        // Inject dynamic blogs into the news page
        if ($htmlFile === 'news.html') {
            try {
                // Check if categories table exists
                if (Schema::hasTable('categories')) {
                    $allBlogs = Blog::with('category')
                        ->where('is_published', true)
                        ->latest()
                        ->get();
                } else {
                    // Fallback: get blogs without category relationship
                    $allBlogs = Blog::where('is_published', true)
                        ->latest()
                        ->get();
                }
            } catch (\Exception $e) {
                // Fallback if there's any error
                $allBlogs = Blog::where('is_published', true)
                    ->latest()
                    ->get();
            }

            $blogsHtml = '';

            foreach ($allBlogs as $blog) {
                $imageUrl = $blog->image_url ?? '/smartpath/assets/img/blog/blog-sidebar-1.jpg';
                $date = $blog->created_at?->format('F d, Y') ?? '';
                $blogUrl = url('/news/' . $blog->slug);
                $excerpt = Str::limit(strip_tags($blog->excerpt ?? $blog->content), 150);

                $blogsHtml .= '
                <div class="postbox__thumb-box mb-80">
                  <div class="postbox__main-thumb mb-30">
                    <img src="' . e($imageUrl) . '" alt="' . e($blog->title) . '" />
                  </div>
                  <div class="postbox__content-box">
                    <div class="postbox__meta">
                      <span><i class="fa-light fa-calendar-days"></i>' . e($date) . '</span>
                      <span><i class="fal fa-user"></i>' . e(is_object($blog->category) ? ($blog->category->name ?? 'News') : ($blog->category ?? 'News')) . '</span>
                    </div>
                    <h4 class="postbox__details-title">
                      <a href="' . e($blogUrl) . '">' . e($blog->title) . '</a>
                    </h4>
                    <p>' . e($excerpt) . '</p>
                    <a class="it-btn mt-15" href="' . e($blogUrl) . '">
                      <span>
                        read more
                        <svg width="17" height="14" viewBox="0 0 17 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M11 1.24023L16 7.24023L11 13.2402" stroke="currentcolor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                          <path d="M1 7.24023H16" stroke="currentcolor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                      </span>
                    </a>
                  </div>
                </div>';
            }

            // Replace content between markers
            $content = preg_replace(
                '/<!-- NEWS_BLOGS_START -->(.*?)<!-- NEWS_BLOGS_END -->/s',
                '<!-- NEWS_BLOGS_START -->' . $blogsHtml . '<!-- NEWS_BLOGS_END -->',
                $content
            );
        }
        
        // Inject dynamic webinars into the webinars page
        if ($htmlFile === 'webinars.html') {
            $allWebinars = Webinar::where('is_published', true)
                ->orderBy('event_date', 'asc')
                ->get();

            $webinarsHtml = '';

            foreach ($allWebinars as $webinar) {
                $imageUrl = $webinar->image_url ?? '/smartpath/assets/img/event/event-1-1.jpg';
                $webinarUrl = url('/webinars/' . $webinar->slug);
                $day = $webinar->event_date?->format('d') ?? '';
                $month = $webinar->event_date?->format('M') ?? ''; // Use short month name (Jan, Feb, etc.)
                $excerpt = Str::limit(strip_tags($webinar->excerpt ?? $webinar->content), 100);

                $webinarsHtml .= '
            <div class="col-xl-4 col-lg-6 col-md-6 mb-30">
              <div class="it-event-2-item-box" style="overflow: hidden;">
                <div class="it-event-2-item">
                  <div class="it-event-2-thumb fix" style="overflow: hidden; position: relative;">
                    <a href="' . e($webinarUrl) . '" style="display: block; overflow: hidden;">
                      <img src="' . e($imageUrl) . '" alt="' . e($webinar->title) . '" style="width: 100%; height: auto; object-fit: cover; display: block;" />
                    </a>
                    <div class="it-event-2-date" style="overflow: hidden;">
                      <span><i>' . e($day) . '</i> <br />' . e($month) . '</span>
                    </div>
                  </div>
                  <div class="it-event-2-content" style="overflow: hidden; word-wrap: break-word;">
                    <h4 class="it-event-2-title" style="word-wrap: break-word; overflow-wrap: break-word;">
                      <a href="' . e($webinarUrl) . '">' . e($webinar->title) . '</a>
                    </h4>
                    <div class="it-event-2-text">
                      <p class="mb-0 pb-10" style="word-wrap: break-word; overflow-wrap: break-word;">' . e($excerpt) . '</p>
                    </div>
                    <div class="it-event-2-meta" style="word-wrap: break-word; overflow-wrap: break-word;">
                      ' . ($webinar->event_time ? '<span><i class="fa-light fa-clock"></i> Time: ' . e($webinar->event_time) . '</span>' : '') . '
                      ' . ($webinar->location ? '<span><i class="fa-light fa-location-dot"></i> ' . e($webinar->location) . '</span>' : '') . '
                    </div>
                  </div>
                </div>
              </div>
            </div>';
            }

            // Replace content between markers
            $content = preg_replace(
                '/<!-- WEBINARS_START -->(.*?)<!-- WEBINARS_END -->/s',
                '<!-- WEBINARS_START -->' . $webinarsHtml . '<!-- WEBINARS_END -->',
                $content
            );
        }
        
        return Response::make($content)->header('Content-Type', 'text/html');
    }
}

// Serve static HTML files from smartpath folder
Route::get('/', function () {
    try {
        return serveSmartPathHtml('index.html');
    } catch (\Exception $e) {
        \Log::error("Error serving index.html: " . $e->getMessage());
        abort(500, $e->getMessage());
    }
});

Route::get('/about', function () {
    try {
        return serveSmartPathHtml('about-us.html');
    } catch (\Exception $e) {
        \Log::error("Error serving about-us.html: " . $e->getMessage());
        abort(500, $e->getMessage());
    }
});

Route::get('/contact', function () {
    try {
        return serveSmartPathHtml('contact.html');
    } catch (\Exception $e) {
        \Log::error("Error serving contact.html: " . $e->getMessage());
        abort(500, $e->getMessage());
    }
});

// Existing Laravel routes
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// News routes (replacing blogs routes)
Route::get('/news', function () {
    try {
        return serveSmartPathHtml('news.html');
    } catch (\Exception $e) {
        \Log::error("Error serving news.html: " . $e->getMessage());
        abort(500, $e->getMessage());
    }
});

Route::get('/news/{slug}', [BlogController::class, 'show'])->name('news.show');

// Webinars routes
Route::get('/webinars', function () {
    try {
        return serveSmartPathHtml('webinars.html');
    } catch (\Exception $e) {
        \Log::error("Error serving webinars.html: " . $e->getMessage());
        abort(500, $e->getMessage());
    }
});

Route::get('/webinars/{slug}', [WebinarController::class, 'show'])->name('webinars.show');

// Keep old /blogs routes for backward compatibility (redirect to /news)
Route::get('/blogs', function () {
    return redirect('/news', 301);
});
Route::get('/blogs/{slug}', function ($slug) {
    return redirect('/news/' . $slug, 301);
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('blogs', AdminBlogController::class);
    Route::resource('webinars', AdminWebinarController::class);
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
});

// Catch-all route for other HTML pages in smartpath (must be last)
// Exclude asset paths and Laravel routes - only match single-segment paths (no slashes)
Route::get('/{page}', function ($page) {
    // Skip if the page contains a slash (it's an asset path) or has a file extension other than .html
    if (strpos($page, '/') !== false) {
        abort(404);
    }
    
    // If it has a dot and doesn't end with .html, it's likely an asset file
    if (strpos($page, '.') !== false && substr($page, -5) !== '.html') {
        abort(404);
    }
    
    // Remove .html extension if present
    $htmlFile = $page;
    if (substr($htmlFile, -5) !== '.html') {
        $htmlFile = $page . '.html';
    }
    
    try {
        return serveSmartPathHtml($htmlFile);
    } catch (\Exception $e) {
        \Log::error("Error serving {$htmlFile}: " . $e->getMessage());
        abort(404);
    }
})->where('page', '^(?!dashboard|blogs|news|webinars|admin|login|register|forgot-password|reset-password|verify-email|confirm-password|profile|api|smartpath|assets|build|storage|favicon|robots)[^\/]*$');

require __DIR__.'/auth.php';
