@extends('layouts.admin')
@section('title','Reviews')
@section('breadcrumb')<li class="breadcrumb-item active">Reviews</li>@endsection
@section('content')
<h5 class="fw-bold mb-4">Review Moderation</h5>
<div class="table-card mb-3">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-5"><input type="text" name="search" class="form-control" placeholder="Vehicle name..." value="{{ request('search') }}"></div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-primary flex-grow-1">Filter</button>
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-light border">Clear</a>
        </div>
    </form>
</div>
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light"><tr><th>Customer</th><th>Vehicle</th><th>Rating</th><th>Review</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($reviews as $r)
                <tr>
                    <td class="small fw-semibold">{{ $r->user_name }}</td>
                    <td class="small">{{ $r->vehicle_name }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            @for($i=1;$i<=5;$i++)<i class="fas fa-star {{ $i<=$r->rating?'text-warning':'text-muted' }}" style="font-size:11px"></i>@endfor
                        </div>
                    </td>
                    <td class="small text-muted" style="max-width:200px">{{ Str::limit($r->review,80) }}</td>
                    <td class="small text-muted">{{ \Carbon\Carbon::parse($r->created_at)->format('M d, Y') }}</td>
                    <td>
                        @if($r->is_approved)<span class="badge bg-success">Approved</span>
                        @else<span class="badge bg-warning">Pending</span>@endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @if(!$r->is_approved)
                            <form method="POST" action="{{ route('admin.reviews.approve', $r->id) }}">
                                @csrf
                                <button class="btn btn-sm btn-success" title="Approve"><i class="fas fa-check"></i></button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.reviews.reject', $r->id) }}">
                                @csrf
                                <button class="btn btn-sm btn-warning" title="Reject"><i class="fas fa-times"></i></button>
                            </form>
                            @endif
                            <form id="delr{{ $r->id }}" method="POST" action="{{ route('admin.reviews.destroy', $r->id) }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-light border text-danger" onclick="confirmDelete('delr{{ $r->id }}')"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No reviews found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $reviews->links() }}</div>
</div>
@endsection
