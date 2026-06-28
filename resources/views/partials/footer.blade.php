{{-- resources/views/partials/footer.blade.php --}}
<footer class="bg-dark text-light pt-4 pb-3 mt-5">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <p class="text-muted small mb-0">&copy; {{ date('Y') }} DriveEase. All rights reserved.</p>
            <div class="d-flex gap-3 mt-2 mt-md-0">
                <a href="{{ route('privacy') }}" class="text-muted small text-decoration-none">Privacy</a>
                <a href="{{ route('terms') }}" class="text-muted small text-decoration-none">Terms</a>
                <a href="{{ route('contact') }}" class="text-muted small text-decoration-none">Contact</a>
            </div>
        </div>
    </div>
</footer>
