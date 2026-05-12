<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Vieja Guardia - Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
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
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-10">
        <div class="max-w-6xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-4">
                    <a href="{{ $dashboardRoute }}" class="text-base font-semibold tracking-tight hover:text-slate-600 transition-colors">La Vieja Guardia</a>
                    <span class="bg-slate-100 text-slate-600 text-xs px-2 py-0.5 rounded-full font-medium border border-slate-200 capitalize">{{ auth()->user()->role ?? 'Admin' }}</span>
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
</body>
</html>
