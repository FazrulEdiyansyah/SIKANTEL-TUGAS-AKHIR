<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <!-- Meta viewport ini adalah kunci utama agar web menyesuaikan ukuran layar (Responsive) -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIKANTEL - Sistem Informasi Kantin Tel-U')</title>

    <!-- Memuat Font Google Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Memuat Tailwind CSS melalui Vite -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="font-primary antialiased bg-gray-50 text-gray-900">
    
    <!-- Tempat di mana konten halaman spesifik (yang belum kita buat) akan ditampilkan -->
    @yield('content')

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmFormSubmit(event, message, type = 'warning') {
            event.preventDefault();
            const form = event.target;
            
            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: type,
                showCancelButton: true,
                confirmButtonColor: '#E31E24',
                cancelButtonColor: '#9CA3AF',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
                    cancelButton: 'rounded-xl px-6 py-2.5 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>
