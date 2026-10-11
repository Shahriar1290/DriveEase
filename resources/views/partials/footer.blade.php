{{-- resources/views/partials/footer.blade.php — shared by the public site and customer area --}}
<footer class="site-footer pt-5">
    <div class="container pt-2">
        <div class="row g-4 g-lg-5 pb-4">
            <div class="col-lg-4">
                <a class="text-white fw-bold fs-4 d-inline-flex align-items-center mb-3" href="{{ route('home') }}">
                    <span class="brand-icon-wrap me-2"><i class="fas fa-car-side"></i></span>
                    <span><span class="brand-accent">Drive</span>Ease</span>
                </a>
                <p class="mb-4" style="max-width: 340px;">Your trusted vehicle rental partner across Bangladesh. Transparent pricing, a verified fleet and support whenever you need it.</p>
                <div class="footer-social d-flex gap-2">
                    <a href="javascript:void(0)" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="javascript:void(0)" aria-label="X (Twitter)"><i class="fab fa-x-twitter"></i></a>
                    <a href="javascript:void(0)" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="javascript:void(0)" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <h6>Explore</h6>
                <ul class="list-unstyled footer-links mb-0">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('vehicles.index') }}">Vehicles</a></li>
                    <li><a href="{{ route('vehicles.compare') }}">Compare</a></li>
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <h6>Support</h6>
                <ul class="list-unstyled footer-links mb-0">
                    <li><a href="{{ route('faq') }}">FAQ</a></li>
                    <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}">Terms &amp; Conditions</a></li>
                    @auth
                        @if(auth()->user()->isCustomer())
                            <li><a href="{{ route('customer.bookings.index') }}">My Bookings</a></li>
                        @endif
                    @else
                        <li><a href="{{ route('login') }}">Sign In</a></li>
                    @endauth
                </ul>
            </div>
            <div class="col-md-6 col-lg-4">
                <h6>Stay in the loop</h6>
                <p class="mb-3">Get the latest deals and new arrivals straight to your inbox.</p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="footer-newsletter d-flex gap-2 mb-4">
                    @csrf
                    <label for="newsletterEmail" class="visually-hidden">Email address</label>
                    <input type="email" name="email" id="newsletterEmail" class="form-control" placeholder="you@example.com" required>
                    <button class="btn btn-primary px-3 flex-shrink-0">Subscribe</button>
                </form>
                <ul class="list-unstyled footer-contact mb-0">
                    <li><i class="fas fa-location-dot"></i><span>123 Main Street, Dhaka, Bangladesh</span></li>
                    <li><i class="fas fa-phone"></i><a href="tel:+8801700000000">+880 1700-000000</a></li>
                    <li><i class="fas fa-envelope"></i><a href="mailto:info@driveease.com">info@driveease.com</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 py-4">
            <p class="mb-0">&copy; {{ date('Y') }} DriveEase. All rights reserved.</p>
            <div class="d-flex gap-3">
                <a href="{{ route('privacy') }}">Privacy</a>
                <a href="{{ route('terms') }}">Terms</a>
                <a href="{{ route('contact') }}">Contact</a>
            </div>
        </div>
    </div>
</footer>

<button type="button" class="back-to-top" id="backToTop" aria-label="Back to top">
    <i class="fas fa-arrow-up"></i>
</button>
