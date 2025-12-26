@extends('layouts.admin')

@section('title', 'Add New Webinar')
@section('page-title', 'Add New Webinar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.webinars.index') }}">Webinars</a></li>
    <li class="breadcrumb-item active">Add New</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.webinars.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Webinar Content</h3>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="form-group">
                            <label for="title">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="Enter webinar title" required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Excerpt -->
                        <div class="form-group">
                            <label for="excerpt">Excerpt</label>
                            <textarea class="form-control @error('excerpt') is-invalid @enderror" id="excerpt" name="excerpt" rows="3" placeholder="Brief description of the webinar (optional)">{{ old('excerpt') }}</textarea>
                            @error('excerpt')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">A short summary that appears on the webinar list.</small>
                        </div>

                        <!-- Content -->
                        <div class="form-group">
                            <label for="content">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="15" placeholder="Write your webinar content here using Markdown..." required>{{ old('content') }}</textarea>
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
                        <h3 class="card-title">Event Details</h3>
                    </div>
                    <div class="card-body">
                        <!-- Event Date -->
                        <div class="form-group">
                            <label for="event_date">Event Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('event_date') is-invalid @enderror" id="event_date" name="event_date" value="{{ old('event_date') }}" required>
                            @error('event_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Event Time -->
                        <div class="form-group">
                            <label for="event_time">Event Time</label>
                            <input type="text" class="form-control @error('event_time') is-invalid @enderror" id="event_time" name="event_time" value="{{ old('event_time') }}" placeholder="e.g., 11:00am - 01:00pm">
                            @error('event_time')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Format: 11:00am - 01:00pm or 4:00 PM – 6:00 PM</small>
                        </div>

                        <!-- Location -->
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location') }}" placeholder="e.g., Online (Zoom) or Physical Address">
                            @error('location')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Registration Link -->
                        <div class="form-group">
                            <label for="registration_link">Registration Link</label>
                            <input type="url" class="form-control @error('registration_link') is-invalid @enderror" id="registration_link" name="registration_link" value="{{ old('registration_link') }}" placeholder="https://example.com/register">
                            @error('registration_link')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">URL where users can register for the webinar.</small>
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
                        <h3 class="card-title">Webinar Settings</h3>
                    </div>
                    <div class="card-body">
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
                            <i class="fas fa-save"></i> Create Webinar
                        </button>
                        <a href="{{ route('admin.webinars.index') }}" class="btn btn-secondary">
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
                        <li class="mb-2">The <strong>excerpt</strong> appears on webinar listing pages.</li>
                        <li class="mb-2">Upload a <strong>featured image</strong> to make your webinar more attractive.</li>
                        <li class="mb-2">Set the <strong>event date</strong> and <strong>time</strong> for when the webinar will take place.</li>
                        <li class="mb-2">Add a <strong>registration link</strong> so users can sign up for the webinar.</li>
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

