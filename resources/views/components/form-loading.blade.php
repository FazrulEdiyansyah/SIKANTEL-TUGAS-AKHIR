{{-- 
    Form Loading Component (Anti Double-Click)
    Otomatis menambahkan loading spinner ke semua tombol submit saat form di-submit.
    Form yang pakai AJAX sendiri bisa ditandai dengan attribute data-no-loading.
    Cukup include <x-form-loading /> di layout.
--}}

<style>
    .btn-loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2.5px solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: btn-spin 0.6s linear infinite;
        vertical-align: middle;
    }
    @keyframes btn-spin {
        to { transform: rotate(360deg); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('submit', function(e) {
            const form = e.target;
            
            // Skip form yang ditandai data-no-loading atau form AJAX
            if (form.hasAttribute('data-no-loading')) return;

            // Cari semua tombol submit di dalam form
            const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            
            submitButtons.forEach(function(btn) {
                // Jangan proses jika sudah loading
                if (btn.dataset.loading === 'true') return;
                
                btn.dataset.loading = 'true';
                btn.disabled = true;
                btn.style.opacity = '0.7';
                btn.style.pointerEvents = 'none';

                // Simpan konten asli
                const originalContent = btn.innerHTML;
                const originalWidth = btn.offsetWidth;
                btn.style.minWidth = originalWidth + 'px';
                btn.dataset.originalContent = originalContent;

                // Ganti konten dengan spinner + teks
                if (btn.tagName === 'BUTTON') {
                    btn.innerHTML = '<span class="btn-loading-spinner"></span><span style="margin-left: 8px;">Memproses...</span>';
                }
            });

            // Safety: Reset setelah 10 detik kalau form tidak redirect (misal: error validasi backend)
            setTimeout(function() {
                submitButtons.forEach(function(btn) {
                    if (btn.dataset.loading === 'true' && btn.dataset.originalContent) {
                        btn.innerHTML = btn.dataset.originalContent;
                        btn.disabled = false;
                        btn.style.opacity = '';
                        btn.style.pointerEvents = '';
                        btn.style.minWidth = '';
                        btn.dataset.loading = 'false';
                    }
                });
            }, 10000);
        });
    });
</script>
