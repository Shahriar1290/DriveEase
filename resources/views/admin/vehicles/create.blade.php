@extends('layouts.admin')
@section('title', isset($vehicle) ? 'Edit Vehicle' : 'Add Vehicle')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.vehicles.index') }}" class="text-decoration-none">Vehicles</a></li>
<li class="breadcrumb-item active">{{ isset($vehicle) ? 'Edit' : 'Add' }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="table-card">
            <h5 class="fw-bold mb-4">{{ isset($vehicle) ? 'Edit Vehicle: '.$vehicle->vehicle_name : 'Add New Vehicle' }}</h5>

            @if($errors->any())
            <div class="alert alert-danger small">
                @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
            @endif

            <form method="POST"
                  action="{{ isset($vehicle) ? route('admin.vehicles.update',$vehicle->id) : route('admin.vehicles.store') }}"
                  enctype="multipart/form-data">
                @csrf
                @if(isset($vehicle)) @method('PUT') @endif

                <div class="row g-4">
                    {{-- Basic Info --}}
                    <div class="col-12"><h6 class="fw-semibold text-muted border-bottom pb-2">Basic Information</h6></div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Vehicle Name <span class="text-danger">*</span></label>
                        <input type="text" name="vehicle_name" class="form-control" value="{{ old('vehicle_name', $vehicle->vehicle_name ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select category</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $vehicle->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->category_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Brand <span class="text-danger">*</span></label>
                        <input type="text" name="brand" class="form-control" value="{{ old('brand', $vehicle->brand ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Model <span class="text-danger">*</span></label>
                        <input type="text" name="model" class="form-control" value="{{ old('model', $vehicle->model ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Year</label>
                        <input type="number" name="year" class="form-control" min="1990" max="{{ date('Y')+1 }}"
                               value="{{ old('year', $vehicle->year ?? date('Y')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Registration Number <span class="text-danger">*</span></label>
                        <input type="text" name="registration_number" class="form-control"
                               value="{{ old('registration_number', $vehicle->registration_number ?? '') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Color</label>
                        <input type="text" name="color" class="form-control" value="{{ old('color', $vehicle->color ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            @foreach(['available','rented','maintenance','inactive'] as $s)
                            <option value="{{ $s }}" {{ old('status', $vehicle->status ?? 'available') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $vehicle->description ?? '') }}</textarea>
                    </div>

                    {{-- Technical --}}
                    <div class="col-12"><h6 class="fw-semibold text-muted border-bottom pb-2 mt-2">Technical Specifications</h6></div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Fuel Type <span class="text-danger">*</span></label>
                        <select name="fuel_type" class="form-select" required>
                            @foreach(['petrol','diesel','electric','hybrid','cng'] as $f)
                            <option value="{{ $f }}" {{ old('fuel_type', $vehicle->fuel_type ?? '') == $f ? 'selected' : '' }}>{{ ucfirst($f) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Transmission <span class="text-danger">*</span></label>
                        <select name="transmission" class="form-select" required>
                            @foreach(['manual','automatic','semi-automatic'] as $t)
                            <option value="{{ $t }}" {{ old('transmission', $vehicle->transmission ?? '') == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Seating Capacity <span class="text-danger">*</span></label>
                        <input type="number" name="seating_capacity" class="form-control" min="1" max="50"
                               value="{{ old('seating_capacity', $vehicle->seating_capacity ?? 5) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Price Per Day ($) <span class="text-danger">*</span></label>
                        <input type="number" name="price_per_day" class="form-control" step="0.01" min="0"
                               value="{{ old('price_per_day', $vehicle->price_per_day ?? '') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Mileage</label>
                        <input type="text" name="mileage" class="form-control" placeholder="e.g. 15 km/l"
                               value="{{ old('mileage', $vehicle->mileage ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Engine CC</label>
                        <input type="text" name="engine_cc" class="form-control" placeholder="e.g. 1600 cc"
                               value="{{ old('engine_cc', $vehicle->engine_cc ?? '') }}">
                    </div>

                    {{-- Features --}}
                    <div class="col-12"><h6 class="fw-semibold text-muted border-bottom pb-2 mt-2">Features</h6></div>
                    @foreach([['air_conditioning','Air Conditioning'],['gps','GPS Navigation'],['bluetooth','Bluetooth'],['usb_charger','USB Charger'],['child_seat','Child Seat']] as [$name,$label])
                    <div class="col-md-3 col-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="{{ $name }}" id="{{ $name }}" value="1"
                                   {{ old($name, $vehicle->$name ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="{{ $name }}">{{ $label }}</label>
                        </div>
                    </div>
                    @endforeach

                    {{-- Images --}}
                    <div class="col-12"><h6 class="fw-semibold text-muted border-bottom pb-2 mt-2">Vehicle Images</h6></div>

                    @if(isset($vehicle) && count($vehicle->images))
                    <div class="col-12">
                        <p class="small fw-semibold text-muted mb-2">Existing Images</p>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($vehicle->images as $img)
                            <div class="position-relative">
                                <img src="{{ vehicle_image_url($img->image) }}" class="rounded border" width="100" height="75" style="object-fit:cover">
                                <div class="form-check position-absolute top-0 end-0 m-1">
                                    <input class="form-check-input" type="checkbox" name="delete_images[]"
                                           value="{{ $img->id }}" id="delimg{{ $img->id }}" style="background:red;border-color:red">
                                </div>
                                <label for="delimg{{ $img->id }}" class="position-absolute bottom-0 start-0 end-0 text-center bg-dark bg-opacity-50 text-white rounded-bottom small" style="font-size:10px;cursor:pointer">✕ Remove</label>
                            </div>
                            @endforeach
                        </div>
                        <small class="text-muted">Check images to remove them.</small>
                    </div>
                    @endif

                    <div class="col-12">
                        <label class="form-label fw-semibold small">{{ isset($vehicle) ? 'Add New Images' : 'Upload Images' }}</label>
                        <input type="file" name="{{ isset($vehicle) ? 'new_images' : 'images' }}[]"
                               class="form-control" accept="image/*" multiple id="imgInput">
                        <small class="text-muted">Max 3MB per image. JPG, PNG, WEBP. First image will be primary.</small>
                        <div id="imgPreview" class="d-flex flex-wrap gap-2 mt-2"></div>
                    </div>

                    <div class="col-12 d-flex gap-3">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-save me-1"></i>{{ isset($vehicle) ? 'Update Vehicle' : 'Save Vehicle' }}
                        </button>
                        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-light border px-4">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('imgInput')?.addEventListener('change', function() {
    const preview = document.getElementById('imgPreview');
    preview.innerHTML = '';
    Array.from(this.files).forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            preview.insertAdjacentHTML('beforeend', `
                <div class="position-relative">
                    <img src="${e.target.result}" class="rounded border" width="90" height="65" style="object-fit:cover">
                    ${i === 0 ? '<span class="position-absolute top-0 start-0 badge bg-primary" style="font-size:9px">Primary</span>' : ''}
                </div>`);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush
