{{-- Inner page banner. Usage: @include('partials.page-hero', ['title' => '...', 'subtitle' => '...', 'eyebrow' => '...']) --}}
<section class="page-hero text-center">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $crumb ?? $title }}</li>
            </ol>
        </nav>
        <h1 class="mb-3">{{ $title }}</h1>
        @isset($subtitle)
            <p class="lead mb-0">{{ $subtitle }}</p>
        @endisset
    </div>
</section>
