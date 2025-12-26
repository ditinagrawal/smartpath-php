@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Info boxes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-newspaper"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Blogs</span>
                    <span class="info-box-number">{{ \App\Models\Blog::count() }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Published</span>
                    <span class="info-box-number">{{ \App\Models\Blog::where('is_published', true)->count() }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Drafts</span>
                    <span class="info-box-number">{{ \App\Models\Blog::where('is_published', false)->count() }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-tags"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Categories</span>
                    <span class="info-box-number">{{ \App\Models\Blog::distinct('category')->count('category') }}</span>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->

    <div class="row">
        <!-- Left col -->
        <section class="col-lg-7 connectedSortable">
            <!-- Recent Blogs -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-newspaper mr-1"></i>
                        Recent Blogs
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-valign-middle">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\Blog::latest()->take(5)->get() as $blog)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.blogs.edit', $blog->id) }}">
                                                {{ Str::limit($blog->title, 40) }}
                                            </a>
                                        </td>
                                        <td>{{ $blog->category }}</td>
                                        <td>
                                            @if($blog->is_published)
                                                <span class="badge badge-success">Published</span>
                                            @else
                                                <span class="badge badge-warning">Draft</span>
                                            @endif
                                        </td>
                                        <td>{{ $blog->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            No blogs yet. <a href="{{ route('admin.blogs.create') }}">Create your first blog!</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.blogs.index') }}" class="uppercase">View All Blogs</a>
                </div>
            </div>
        </section>
        <!-- /.Left col -->

        <!-- Right col -->
        <section class="col-lg-5 connectedSortable">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt mr-1"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-plus mb-2" style="font-size: 24px; display: block;"></i>
                                New Blog
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="{{ route('admin.blogs.index') }}" class="btn btn-info btn-block">
                                <i class="fas fa-list mb-2" style="font-size: 24px; display: block;"></i>
                                All Blogs
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="/" target="_blank" class="btn btn-success btn-block">
                                <i class="fas fa-eye mb-2" style="font-size: 24px; display: block;"></i>
                                View Site
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('profile.edit') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-user-cog mb-2" style="font-size: 24px; display: block;"></i>
                                Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Welcome Card -->
            <div class="card bg-gradient-primary">
                <div class="card-body">
                    <h5 class="card-title text-white">Welcome back, {{ Auth::user()->name }}!</h5>
                    <p class="card-text text-white-50">
                        You are logged in to the ByteWork admin panel. From here you can manage all your blog content.
                    </p>
                </div>
            </div>
        </section>
        <!-- /.Right col -->
    </div>
@endsection
