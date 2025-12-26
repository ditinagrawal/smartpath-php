@extends('layouts.admin')

@section('title', 'Edit Webinar')
@section('page-title', 'Edit Webinar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.webinars.index') }}">Webinars</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.webinars.update', $webinar->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Webinar Content</h3>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="form-group">
                            <label for="title">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $webinar->title) }}" placeholder="Enter webinar title" required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Excerpt -->
                        <div class="form-group">
                            <label for="excerpt">Excerpt</label>
                            <textarea class="form-control @error('excerpt') is-invalid @enderror" id="excerpt" name="excerpt" rows="3" placeholder="Brief description of the webinar (optional)">{{ old('excerpt', $webinar->excerpt) }}</textarea>
                            @error('excerpt')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">A short summary that appears on the webinar list.</small>
                        </div>

                        <!-- Content -->
                        <div class="form-group">
                            <label for="content">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="15" placeholder="Write your webinar content here using Markdown..." required>{{ old('content', $webinar->content) }}</textarea>
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
                            <input type="date" class="form-control @error('event_date') is-invalid @enderror" id="event_date" name="event_date" value="{{ old('event_date', $webinar->event_date?->format('Y-m-d')) }}" required>
                            @error('event_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Event Time -->
                        <div class="form-group">
                            <label for="event_time">Event Time</label>
                            <input type="text" class="form-control @error('event_time') is-invalid @enderror" id="event_time" name="event_time" value="{{ old('event_time', $webinar->event_time) }}" placeholder="e.g., 11:00am - 01:00pm">
                            @error('event_time')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Format: 11:00am - 01:00pm or 4:00 PM – 6:00 PM</small>
                        </div>

                        <!-- Location -->
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $webinar->location) }}" placeholder="e.g., Online (Zoom) or Physical Address">
                            @error('location')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Registration Link -->
                        <div class="form-group">
                            <label for="registration_link">Registration Link</label>
                            <input type="url" class="form-control @error('registration_link') is-invalid @enderror" id="registration_link" name="registration_link" value="{{ old('registration_link', $webinar->registration_link) }}" placeholder="https://example.com/register">
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
                        <!-- Current Image -->
                        @if($webinar->image)
                            <div class="form-group" id="currentImageContainer">
                                <label>Current Image:</label>
                                <div class="position-relative d-inline-block">
                                    <img src="{{ $webinar->image_url }}" alt="Current Image" class="img-fluid rounded" style="max-height: 200px;">
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
                            <label for="image">{{ $webinar->image ? 'Replace Image' : 'Upload Image' }}</label>
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
                        <h3 class="card-title">Webinar Settings</h3>
                    </div>
                    <div class="card-body">
                        <!-- Published -->
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_published" name="is_published" {{ old('is_published', $webinar->is_published) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_published">Published</label>
                            </div>
                            <small class="form-text text-muted">Uncheck to save as draft.</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Webinar
                        </button>
                        <a href="{{ route('admin.webinars.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <!-- Webinar Info Card -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Webinar Info</h3>
                </div>
                <div class="card-body">
                    <dl>
                        <dt>Slug</dt>
                        <dd><code>{{ $webinar->slug }}</code></dd>
                        
                        <dt>Event Date</dt>
                        <dd>{{ $webinar->event_date?->format('M d, Y') ?? 'N/A' }}</dd>
                        
                        <dt>Created</dt>
                        <dd>{{ $webinar->created_at?->format('M d, Y h:i A') ?? 'N/A' }}</dd>
                        
                        <dt>Last Updated</dt>
                        <dd>{{ $webinar->updated_at?->format('M d, Y h:i A') ?? 'N/A' }}</dd>
                        
                        <dt>Status</dt>
                        <dd>
                            @if($webinar->is_published)
                                <span class="badge badge-success">Published</span>
                            @else
                                <span class="badge badge-warning">Draft</span>
                            @endif
                        </dd>

                        <dt>Featured Image</dt>
                        <dd>
                            @if($webinar->image)
                                <span class="badge badge-success"><i class="fas fa-check"></i> Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </dd>
                    </dl>
                </div>
                <div class="card-footer">
                    <a href="{{ url('/webinars/' . $webinar->slug) }}" class="btn btn-info btn-block" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View Webinar
                    </a>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Once deleted, this webinar cannot be recovered.</p>
                    <form action="{{ route('admin.webinars.destroy', $webinar->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this webinar? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i> Delete Webinar
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

