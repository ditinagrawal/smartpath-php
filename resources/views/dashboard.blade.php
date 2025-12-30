@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    @php
        $totalBlogs = \App\Models\Blog::count();
        $publishedBlogs = \App\Models\Blog::where('is_published', true)->count();
        $draftBlogs = \App\Models\Blog::where('is_published', false)->count();
        $totalCategories = \App\Models\Category::count();
        $totalWebinars = \App\Models\Webinar::count();
        $publishedWebinars = \App\Models\Webinar::where('is_published', true)->count();
        $totalRegistrations = \App\Models\WebinarRegistration::count();
        $recentBlogs = \App\Models\Blog::with('category')->latest()->take(5)->get();
        $recentWebinars = \App\Models\Webinar::latest()->take(5)->get();
        $recentRegistrations = \App\Models\WebinarRegistration::with('webinar')->latest()->take(5)->get();
    @endphp

    <!-- Statistics Cards Row 1 -->
    <div class="row">
        <!-- Blogs Statistics -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalBlogs }}</h3>
                    <p>Total Blogs</p>
                </div>
                <div class="icon">
                    <i class="fas fa-newspaper"></i>
                </div>
                <a href="{{ route('admin.blogs.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $publishedBlogs }}</h3>
                    <p>Published Blogs</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('admin.blogs.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $draftBlogs }}</h3>
                    <p>Draft Blogs</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('admin.blogs.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $totalCategories }}</h3>
                    <p>Categories</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tags"></i>
                </div>
                <a href="{{ route('admin.categories.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row 2 -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalWebinars }}</h3>
                    <p>Total Webinars</p>
                </div>
                <div class="icon">
                    <i class="fas fa-video"></i>
                </div>
                <a href="{{ route('admin.webinars.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-teal" style="background-color: #20c997 !important;">
                <div class="inner">
                    <h3>{{ $publishedWebinars }}</h3>
                    <p>Published Webinars</p>
                </div>
                <div class="icon">
                    <i class="fas fa-broadcast-tower"></i>
                </div>
                <a href="{{ route('admin.webinars.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-purple" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;">
                <div class="inner">
                    <h3>{{ $totalRegistrations }}</h3>
                    <p>Total Registrations</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.webinar-registrations.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-orange" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;">
                <div class="inner">
                    <h3>{{ $totalRegistrations > 0 ? round(($totalRegistrations / max($publishedWebinars, 1)) * 10) / 10 : 0 }}</h3>
                    <p>Avg. per Webinar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <a href="{{ route('admin.webinar-registrations.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
            <!-- Recent Blogs -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-transparent">
                    <h3 class="card-title">
                        <i class="fas fa-newspaper mr-2"></i>
                        Recent Blogs
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBlogs as $blog)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="text-dark">
                                                <strong>{{ Str::limit($blog->title, 35) }}</strong>
                                            </a>
                                        </td>
                                        <td>
                                            @if($blog->category_id && $blog->relationLoaded('category') && $blog->category)
                                                <span class="badge badge-info">{{ $blog->category->name }}</span>
                                            @elseif(isset($blog->attributes['category']) && $blog->attributes['category'])
                                                <span class="badge badge-info">{{ $blog->attributes['category'] }}</span>
                                            @else
                                                <span class="badge badge-secondary">No Category</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($blog->is_published)
                                                <span class="badge badge-success">Published</span>
                                            @else
                                                <span class="badge badge-warning">Draft</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $blog->created_at?->format('M d, Y') ?? 'N/A' }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-newspaper fa-2x mb-2"></i><br>
                                            No blogs yet. <a href="{{ route('admin.blogs.create') }}">Create your first blog!</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-sm btn-info float-right">
                        View All Blogs <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Webinars -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-transparent">
                    <h3 class="card-title">
                        <i class="fas fa-video mr-2"></i>
                        Recent Webinars
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.webinars.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Registrations</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentWebinars as $webinar)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.webinars.edit', $webinar->id) }}" class="text-dark">
                                                <strong>{{ Str::limit($webinar->title, 35) }}</strong>
                                            </a>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $webinar->event_date?->format('M d, Y') ?? 'N/A' }}
                                            </small>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.webinar-registrations.show', $webinar->id) }}" class="badge badge-info">
                                                {{ $webinar->registrations()->count() }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($webinar->is_published)
                                                <span class="badge badge-success">Published</span>
                                            @else
                                                <span class="badge badge-warning">Draft</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-video fa-2x mb-2"></i><br>
                                            No webinars yet. <a href="{{ route('admin.webinars.create') }}">Create your first webinar!</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <a href="{{ route('admin.webinars.index') }}" class="btn btn-sm btn-info float-right">
                        View All Webinars <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="row">
        <!-- Recent Registrations -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header border-transparent">
                    <h3 class="card-title">
                        <i class="fas fa-users mr-2"></i>
                        Recent Webinar Registrations
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.webinar-registrations.index') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-list"></i> View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Webinar</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentRegistrations as $registration)
                                    <tr>
                                        <td><strong>{{ $registration->name }}</strong></td>
                                        <td>{{ $registration->email }}</td>
                                        <td>{{ $registration->phone }}</td>
                                        <td>
                                            <a href="{{ route('admin.webinars.edit', $registration->webinar_id) }}" class="text-dark">
                                                {{ Str::limit($registration->webinar->title, 30) }}
                                            </a>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $registration->created_at?->format('M d, Y') ?? 'N/A' }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-users fa-2x mb-2"></i><br>
                                            No registrations yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Welcome -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt mr-2"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary btn-block btn-lg">
                                <i class="fas fa-plus mb-2" style="font-size: 28px; display: block;"></i>
                                <small>New Blog</small>
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="{{ route('admin.webinars.create') }}" class="btn btn-info btn-block btn-lg">
                                <i class="fas fa-video mb-2" style="font-size: 28px; display: block;"></i>
                                <small>New Webinar</small>
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-success btn-block btn-lg">
                                <i class="fas fa-tags mb-2" style="font-size: 28px; display: block;"></i>
                                <small>New Category</small>
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="/" target="_blank" class="btn btn-warning btn-block btn-lg">
                                <i class="fas fa-eye mb-2" style="font-size: 28px; display: block;"></i>
                                <small>View Site</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Welcome Card -->
            <div class="card bg-gradient-primary">
                <div class="card-body">
                    <h5 class="card-title text-white mb-3">
                        <i class="fas fa-user-circle mr-2"></i>
                        Welcome back, {{ Auth::user()->name }}!
                    </h5>
                    <p class="card-text text-white-50 mb-3">
                        You're managing the ByteWork admin panel. Here's what you can do:
                    </p>
                    <ul class="text-white-50 mb-0" style="list-style: none; padding-left: 0;">
                        <li><i class="fas fa-check-circle mr-2"></i> Manage blogs & categories</li>
                        <li><i class="fas fa-check-circle mr-2"></i> Create & manage webinars</li>
                        <li><i class="fas fa-check-circle mr-2"></i> View registrations</li>
                        <li><i class="fas fa-check-circle mr-2"></i> Monitor statistics</li>
                    </ul>
                </div>
            </div>

            <!-- Statistics Summary -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-2"></i>
                        Quick Stats
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border-right">
                                <h4 class="mb-0 text-primary">{{ $totalBlogs }}</h4>
                                <small class="text-muted">Blogs</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="mb-0 text-info">{{ $totalWebinars }}</h4>
                            <small class="text-muted">Webinars</small>
                        </div>
                        <div class="col-6">
                            <div class="border-right">
                                <h4 class="mb-0 text-success">{{ $totalCategories }}</h4>
                                <small class="text-muted">Categories</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-0 text-warning">{{ $totalRegistrations }}</h4>
                            <small class="text-muted">Registrations</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .small-box {
        border-radius: 0.25rem;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        display: block;
        margin-bottom: 20px;
        position: relative;
    }
    
    .small-box > .inner {
        padding: 10px;
    }
    
    .small-box > .small-box-footer {
        background-color: rgba(0,0,0,.1);
        color: rgba(255,255,255,.8);
        display: block;
        padding: 3px 0;
        position: relative;
        text-align: center;
        text-decoration: none;
        z-index: 10;
    }
    
    .small-box:hover {
        text-decoration: none;
        color: #f9f9f9;
    }
    
    .small-box:hover .icon {
        font-size: 95px;
    }
    
    .small-box .icon {
        transition: all .3s linear;
        position: absolute;
        top: -10px;
        right: 10px;
        z-index: 0;
        font-size: 90px;
        color: rgba(0,0,0,.15);
    }
    
    .small-box h3 {
        font-size: 2.2rem;
        font-weight: bold;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }
    
    .small-box p {
        font-size: 1rem;
    }
    
    .bg-gradient-purple {
        color: #fff;
    }
    
    .bg-gradient-orange {
        color: #fff;
    }
    
    .card {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        margin-bottom: 20px;
    }
    
    .card-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0,0,0,.125);
        padding: 0.75rem 1.25rem;
    }
    
    .card-title {
        margin-bottom: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
</style>
@endsection
