<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Vieja Guardia - Panel</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-[#FAFAFA] min-h-screen text-slate-900 antialiased selection:bg-slate-200">
    @php
        $dashboardRoute = url('/dashboard');
        if (auth()->check()) {
            if (auth()->user()->role === 'administrador') {
                $dashboardRoute = route('admin.dashboard');
            } elseif (auth()->user()->role === 'secretaria') {
                $dashboardRoute = route('secretaria.dashboard');
            }
        }
    @endphp
    
    <!-- Navbar -->
    <nav class="bg-[#F5EBD6] border-b border-[#E5D5B5] sticky top-0 z-10">
        <div class="max-w-6xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-4">
                    <a href="{{ $dashboardRoute }}" class="text-xl font-serif font-bold tracking-tight text-primary hover:opacity-80 transition-opacity flex items-center gap-2">
                        <span class="text-2xl">🧔🏻‍♂️</span> La Vieja Guardia
                    </a>
                    <span class="bg-slate-100 text-slate-600 text-xs px-2 py-0.5 rounded-full font-medium border border-slate-200 capitalize">{{ auth()->user()->role ?? 'Administrador' }}</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-slate-500 font-medium hidden sm:block">{{ auth()->user()->name ?? 'Usuario' }}</span>
                    <div class="h-4 w-px bg-slate-200 hidden sm:block"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-6 lg:px-8 py-10">
        @if(!request()->routeIs('*.dashboard'))
            <div class="mb-6">
                <a href="{{ $dashboardRoute }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
                    &larr; Volver al Panel
                </a>
            </div>
        @endif

        @yield('content')
    </main>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formsEliminar = document.querySelectorAll('.form-eliminar');
            formsEliminar.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const title = this.dataset.title || '¿Estás seguro?';
                    const warning = this.dataset.warning || 'Esta acción no se puede deshacer.';
                    
                    Swal.fire({
                        title: title,
                        text: warning,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
