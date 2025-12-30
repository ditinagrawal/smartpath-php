@extends('layouts.admin')

@section('title', 'Edit Blog')
@section('page-title', 'Edit Blog')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Blogs</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Blog Content</h3>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="form-group">
                            <label for="title">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $blog->title) }}" placeholder="Enter blog title" required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Excerpt -->
                        <div class="form-group">
                            <label for="excerpt">Excerpt</label>
                            <textarea class="form-control @error('excerpt') is-invalid @enderror" id="excerpt" name="excerpt" rows="3" placeholder="Brief description of the blog (optional)">{{ old('excerpt', $blog->excerpt) }}</textarea>
                            @error('excerpt')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">A short summary that appears on the blog list.</small>
                        </div>

                        <!-- Content -->
                        <div class="form-group">
                            <label for="content">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="15" placeholder="Write your blog content here using Markdown..." required>{{ old('content', $blog->content) }}</textarea>
                            @error('content')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fab fa-markdown"></i> Markdown supported: **bold**, *italic*, # heading, - lists, `code`, [links](url), ![images](url)
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Featured Image</h3>
                    </div>
                    <div class="card-body">
                        <!-- Current Image -->
                        @if($blog->image)
                            <div class="form-group" id="currentImageContainer">
                                <label>Current Image:</label>
                                <div class="position-relative d-inline-block">
                                    <img src="{{ $blog->image_url }}" alt="Current Image" class="img-fluid rounded" style="max-height: 200px;">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute" style="top: 5px; right: 5px;" onclick="markImageForRemoval()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="remove_image" id="remove_image" value="0">
                            </div>
                            <div class="alert alert-warning" id="removeImageAlert" style="display: none;">
                                <i class="fas fa-exclamation-triangle"></i> Image will be removed when you save.
                                <button type="button" class="btn btn-sm btn-secondary ml-2" onclick="cancelImageRemoval()">Cancel</button>
                            </div>
                        @endif

                        <!-- Image Upload -->
                        <div class="form-group">
                            <label for="image">{{ $blog->image ? 'Replace Image' : 'Upload Image' }}</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                <label class="custom-file-label" for="image">Choose file</label>
                            </div>
                            @error('image')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Accepted formats: JPEG, PNG, JPG, GIF, WebP. Max size: 2MB</small>
                        </div>
                        
                        <!-- New Image Preview -->
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <label>New Image Preview:</label>
                            <div class="position-relative d-inline-block">
                                <img id="preview" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                <button type="button" class="btn btn-danger btn-sm position-absolute" style="top: 5px; right: 5px;" onclick="removePreview()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Blog Settings</h3>
                    </div>
                    <div class="card-body">
                        <!-- Category -->
                        <div class="form-group">
                            <label for="category_id">Category <span class="text-danger">*</span></label>
                            <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $blog->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                <a href="{{ route('admin.categories.create') }}" target="_blank">
                                    <i class="fas fa-plus"></i> Create a new category
                                </a>
                            </small>
                        </div>

                        <!-- Published -->
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_published" name="is_published" {{ old('is_published', $blog->is_published) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_published">Published</label>
                            </div>
                            <small class="form-text text-muted">Uncheck to save as draft.</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Blog
                        </button>
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <!-- Blog Info Card -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Blog Info</h3>
                </div>
                <div class="card-body">
                    <dl>
                        <dt>Slug</dt>
                        <dd><code>{{ $blog->slug }}</code></dd>
                        
                        <dt>Created</dt>
                        <dd>{{ $blog->created_at?->format('M d, Y h:i A') ?? 'N/A' }}</dd>
                        
                        <dt>Last Updated</dt>
                        <dd>{{ $blog->updated_at?->format('M d, Y h:i A') ?? 'N/A' }}</dd>
                        
                        <dt>Status</dt>
                        <dd>
                            @if($blog->is_published)
                                <span class="badge badge-success">Published</span>
                            @else
                                <span class="badge badge-warning">Draft</span>
                            @endif
                        </dd>

                        <dt>Featured Image</dt>
                        <dd>
                            @if($blog->image)
                                <span class="badge badge-success"><i class="fas fa-check"></i> Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </dd>
                    </dl>
                </div>
                <div class="card-footer">
                    <a href="{{ url('/news/' . $blog->slug) }}" class="btn btn-info btn-block" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View Blog
                    </a>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Once deleted, this blog cannot be recovered.</p>
                    <form action="{{ route('admin.blogs.destroy', $blog->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this blog? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i> Delete Blog
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Update file input label
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        var fileName = e.target.files[0] ? e.target.files[0].name : 'Choose file';
        e.target.nextElementSibling.textContent = fileName;
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removePreview() {
        document.getElementById('image').value = '';
        document.getElementById('imagePreview').style.display = 'none';
        document.querySelector('.custom-file-label').textContent = 'Choose file';
    }

    function markImageForRemoval() {
        document.getElementById('remove_image').value = '1';
        document.getElementById('currentImageContainer').style.display = 'none';
        document.getElementById('removeImageAlert').style.display = 'block';
    }

    function cancelImageRemoval() {
        document.getElementById('remove_image').value = '0';
        document.getElementById('currentImageContainer').style.display = 'block';
        document.getElementById('removeImageAlert').style.display = 'none';
    }
</script>
@endsection
