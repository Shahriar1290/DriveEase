@extends('layouts.admin')
@section('title', isset($category) ? 'Edit Category' : 'Add Category')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}" class="text-decoration-none">Categories</a></li>
<li class="breadcrumb-item active">{{ isset($category) ? 'Edit' : 'Add' }}</li>
@endsection
@section('content')
<div class="row justify-content-center"><div class="col-lg-7">
<div class="table-card">
    <h5 class="fw-bold mb-4">{{ isset($category) ? 'Edit Category' : 'Add New Category' }}</h5>
    @if($errors->any())
    <div class="alert alert-danger small">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>
    @endif
    <form method="POST"
          action="{{ isset($category) ? route('admin.categories.update',$category->id) : route('admin.categories.store') }}"
          enctype="multipart/form-data">
        @csrf @if(isset($category)) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold small">Category Name <span class="text-danger">*</span></label>
                <input type="text" name="category_name" class="form-control" required value="{{ old('category_name', $category->category_name ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold small">Icon Class (Font Awesome)</label>
                <input type="text" name="icon" class="form-control" placeholder="e.g. fas fa-car" value="{{ old('icon', $category->icon ?? '') }}">
                <small class="text-muted">Visit fontawesome.com for icon classes</small>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold small">Description</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description', $category->description ?? '') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold small">Category Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                @if(isset($category) && $category->image)
                <div class="mt-2"><img src="{{ category_image_url($category->image) }}" height="60" class="rounded border"></div>
                @endif
            </div>
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
            <div class="col-12 d-flex gap-3">
                <button type="submit" class="btn btn-primary px-5"><i class="fas fa-save me-1"></i>Save</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-light border px-4">Cancel</a>
            </div>
        </div>
    </form>
</div>
</div></div>
@endsection
