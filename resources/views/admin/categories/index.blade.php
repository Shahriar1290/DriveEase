@extends('layouts.admin')
@section('title','Categories')
@section('breadcrumb')<li class="breadcrumb-item active">Categories</li>@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Vehicle Categories</h5>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Category</a>
</div>
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light"><tr><th>#</th><th>Name</th><th>Icon</th><th>Vehicles</th><th>Status</th><th>Order</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td class="text-muted small">{{ $cat->id }}</td>
                    <td class="fw-semibold">{{ $cat->category_name }}</td>
                    <td><i class="{{ $cat->icon ?? 'fas fa-car' }} text-primary"></i></td>
                    <td><span class="badge bg-primary-subtle text-primary">{{ $cat->vehicles_count }}</span></td>
                    <td>
                        @if($cat->is_active)<span class="badge bg-success">Active</span>
                        @else<span class="badge bg-secondary">Inactive</span>@endif
                    </td>
                    <td>{{ $cat->sort_order }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-sm btn-light border"><i class="fas fa-edit"></i></a>
                            <form id="delcat{{ $cat->id }}" method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-light border text-danger" onclick="confirmDelete('delcat{{ $cat->id }}','Delete this category?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No categories.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $categories->links() }}</div>
</div>
@endsection
