{{-- resources/views/partials/scripts.blade.php — shared front-end behaviour for the public site and customer area --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.1/sweetalert2.all.min.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const escapeHtml = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

// Navbar: compact + shadow once the page scrolls; back-to-top visibility
(() => {
    const nav = document.getElementById('mainNavbar');
    const toTop = document.getElementById('backToTop');
    const onScroll = () => {
        const y = window.scrollY;
        nav?.classList.toggle('navbar-scrolled', y > 24);
        toTop?.classList.toggle('show', y > 600);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    toTop?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
})();

// Scroll reveal for elements marked [data-reveal]
(() => {
    const items = document.querySelectorAll('[data-reveal]');
    if (!items.length || !('IntersectionObserver' in window)) return;
    document.documentElement.classList.add('js-reveal');
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (!e.isIntersecting) return;
            const delay = parseInt(e.target.dataset.revealDelay || 0, 10);
            setTimeout(() => e.target.classList.add('revealed'), delay);
            io.unobserve(e.target);
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    items.forEach(el => io.observe(el));
})();

// Live search with keyboard support
(() => {
    const input = document.getElementById('navSearch');
    const dropdown = document.getElementById('searchDropdown');
    if (!input) return;
    let timer, active = -1;

    const close = () => { dropdown.classList.add('d-none'); active = -1; };
    const links = () => [...dropdown.querySelectorAll('a')];
    const highlight = () => links().forEach((a, i) => a.classList.toggle('bg-light', i === active));

    input.addEventListener('input', function () {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 2) { close(); return; }
        timer = setTimeout(() => {
            fetch(`{{ route('vehicles.search') }}?q=${encodeURIComponent(q)}`, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    active = -1;
                    dropdown.innerHTML = data.length
                        ? data.map(v => `
                            <a href="${escapeHtml(v.url)}" class="d-flex align-items-center gap-3 px-3 py-2 text-decoration-none text-dark border-bottom">
                                <img src="${escapeHtml(v.image)}" width="48" height="34" class="rounded object-fit-cover flex-shrink-0" alt="">
                                <div class="overflow-hidden">
                                    <div class="fw-semibold small text-truncate">${escapeHtml(v.name)}</div>
                                    <div class="text-muted" style="font-size:11px">${escapeHtml(v.brand)} · TK ${escapeHtml(v.price)}/day</div>
                                </div>
                            </a>`).join('')
                        : `<div class="px-3 py-3 small text-muted"><i class="fas fa-magnifying-glass me-2"></i>No vehicles match “${escapeHtml(q)}”</div>`;
                    dropdown.classList.remove('d-none');
                })
                .catch(close);
        }, 250);
    });

    input.addEventListener('keydown', e => {
        const items = links();
        if (dropdown.classList.contains('d-none') || !items.length) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); active = (active + 1) % items.length; highlight(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); active = (active - 1 + items.length) % items.length; highlight(); }
        else if (e.key === 'Enter' && active >= 0) { e.preventDefault(); items[active].click(); }
        else if (e.key === 'Escape') { close(); }
    });

    document.addEventListener('click', e => { if (!e.target.closest('#navSearchForm')) close(); });
})();

// Wishlist toggle without a page reload (falls back to a normal POST if JS fails)
document.addEventListener('submit', e => {
    const form = e.target.closest('form[data-wishlist]');
    if (!form) return;
    e.preventDefault();
    const btn = form.querySelector('button');
    btn.disabled = true;
    fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: new FormData(form),
    })
        .then(r => { if (!r.ok) throw new Error(); return r.json(); })
        .then(data => {
            btn.classList.toggle('active', data.in_wishlist);
            btn.classList.remove('pop'); void btn.offsetWidth; btn.classList.add('pop');
            btn.setAttribute('aria-pressed', data.in_wishlist);
            btn.title = data.in_wishlist ? 'Remove from wishlist' : 'Save to wishlist';
            Toast.fire({ icon: data.in_wishlist ? 'success' : 'info', title: data.message });
        })
        .catch(() => form.submit())
        .finally(() => { btn.disabled = false; });
});

// SweetAlert confirm for destructive actions
document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function (e) {
        e.preventDefault();
        const form = this.closest('form') || document.querySelector(this.dataset.form || '#deleteForm');
        Swal.fire({
            title: 'Are you sure?',
            text: this.dataset.confirm || 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, proceed',
        }).then(result => { if (result.isConfirmed) form.submit(); });
    });
});

// Flash messages
@if(session('success'))
Toast.fire({ icon: 'success', title: @js(session('success')) });
@endif
@if(session('error'))
Toast.fire({ icon: 'error', title: @js(session('error')), timer: 5000 });
@endif
</script>
