@extends('layouts.admin')

@section('title', 'Add New Blog')
@section('page-title', 'Add New Blog')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Blogs</a></li>
    <li class="breadcrumb-item active">Add New</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Blog Content</h3>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="form-group">
                            <label for="title">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="Enter blog title" required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Excerpt -->
                        <div class="form-group">
                            <label for="excerpt">Excerpt</label>
                            <textarea class="form-control @error('excerpt') is-invalid @enderror" id="excerpt" name="excerpt" rows="3" placeholder="Brief description of the blog (optional)">{{ old('excerpt') }}</textarea>
                            @error('excerpt')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">A short summary that appears on the blog list.</small>
                        </div>

                        <!-- Content -->
                        <div class="form-group">
                            <label for="content">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="15" placeholder="Write your blog content here using Markdown..." required>{{ old('content') }}</textarea>
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
                        <!-- Image Upload -->
                        <div class="form-group">
                            <label for="image">Upload Image</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                <label class="custom-file-label" for="image">Choose file</label>
                            </div>
                            @error('image')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Accepted formats: JPEG, PNG, JPG, GIF, WebP. Max size: 2MB</small>
                        </div>
                        
                        <!-- Image Preview -->
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <label>Preview:</label>
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
                            <label for="category">Category <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category') }}" placeholder="e.g., Technology, Business, Development" required>
                            @error('category')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Published -->
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_published" name="is_published" {{ old('is_published', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_published">Publish immediately</label>
                            </div>
                            <small class="form-text text-muted">Uncheck to save as draft.</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Blog
                        </button>
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <!-- Help Card -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Tips</h3>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li class="mb-2">The <strong>title</strong> will be used to generate the URL slug automatically.</li>
                        <li class="mb-2">The <strong>excerpt</strong> appears on blog listing pages.</li>
                        <li class="mb-2">Upload a <strong>featured image</strong> to make your blog more attractive.</li>
                        <li class="mb-2">Use the <strong>category</strong> to organize your blogs.</li>
                        <li class="mb-2">Uncheck <strong>publish</strong> to save as a draft that won't be visible to visitors.</li>
                        <li><strong>Markdown</strong> is supported in content for formatting.</li>
                    </ul>
                </div>
            </div>

            <!-- Markdown Help Card -->
            <div class="card card-secondary collapsed-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fab fa-markdown"></i> Markdown Cheatsheet</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="display: none;">
                    <table class="table table-sm table-bordered">
                        <tr><td><code># Heading 1</code></td><td>Large heading</td></tr>
                        <tr><td><code>## Heading 2</code></td><td>Medium heading</td></tr>
                        <tr><td><code>**bold**</code></td><td><strong>bold</strong></td></tr>
                        <tr><td><code>*italic*</code></td><td><em>italic</em></td></tr>
                        <tr><td><code>[link](url)</code></td><td>Hyperlink</td></tr>
                        <tr><td><code>![alt](image-url)</code></td><td>Image</td></tr>
                        <tr><td><code>- item</code></td><td>Bullet list</td></tr>
                        <tr><td><code>1. item</code></td><td>Numbered list</td></tr>
                        <tr><td><code>`code`</code></td><td>Inline code</td></tr>
                        <tr><td><code>> quote</code></td><td>Blockquote</td></tr>
                        <tr><td><code>---</code></td><td>Horizontal line</td></tr>
                    </table>
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
</script>
@endsection
