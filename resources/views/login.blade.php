<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Vieja Guardia - Login</title>
    @vite(['resources/css/app.css'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body
    class="bg-white min-h-screen font-sans text-slate-900 antialiased selection:bg-secondary selection:text-primary flex">

    <!-- Left Section: Background Image -->
    <div class="relative hidden lg:flex lg:w-1/2 bg-primary overflow-hidden items-center justify-center">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="/images/login_bg.png" alt="Barber Tools"
                class="w-full h-full object-cover opacity-50 mix-blend-overlay">
            <div class="absolute inset-0 bg-gradient-to-tr from-primary via-primary/80 to-transparent"></div>
        </div>

        <!-- Decoration / Content on image -->
        <div class="relative z-10 flex flex-col items-center px-12 text-center text-white">
            <div
                class="w-24 h-24 mb-6 bg-primary/80 backdrop-blur-sm border-2 border-secondary rounded-full flex items-center justify-center shadow-[0_0_30px_rgba(197,160,89,0.2)]">
                <span class="text-5xl">🧔🏻</span>
            </div>
            <h1 class="text-4xl font-bold mb-4 tracking-tight">El estilo es una actitud.</h1>
            <p class="text-secondary font-medium text-lg max-w-md">
                Accede al panel de administración para gestionar citas, servicios y a tu equipo de profesionales.
            </p>
        </div>
    </div>

    <!-- Right Section: Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 lg:px-16 bg-[#FAFAFA]">
        <div class="w-full max-w-md">
            <!-- Mobile Logo -->
            <div class="mb-10 text-center lg:hidden">
                <div class="flex justify-center mb-4">
                    <span class="text-6xl">🧔🏻‍♂️💈</span>
                </div>
            </div>

            <div class="mb-10">
                <h2 class="text-3xl font-bold tracking-tight text-primary mb-2">Bienvenido de vuelta</h2>
                <p class="text-slate-500 font-medium">Ingresa tus credenciales para continuar.</p>
            </div>

            @if (session('status'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Mensaje',
                            text: '{{ session('status') }}',
                            confirmButtonColor: '#C5A059'
                        });
                    });
                </script>
            @endif

            @if ($errors->any())
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de acceso',
                            html: '<ul class="list-disc pl-5 text-left">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                            confirmButtonColor: '#ef4444'
                        });
                    });
                </script>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label for="email" class="block text-sm font-semibold text-slate-700">Correo Electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="block w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-base placeholder-slate-400 focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all shadow-sm">
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-semibold text-slate-700">Contraseña</label>
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="block w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-base placeholder-slate-400 focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all shadow-sm">
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-3.5 px-4 mt-4 border border-transparent rounded-xl text-base font-bold text-primary bg-secondary hover:bg-secondary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary transition-all shadow-[0_4px_15px_rgba(197,160,89,0.3)] hover:shadow-[0_4px_20px_rgba(197,160,89,0.5)] active:scale-[0.98]">
                    Ingresar
                </button>
            </form>
        </div>
    </div>
</body>

</html>
