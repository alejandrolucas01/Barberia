<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Vieja Guardia - Inicio</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-primary min-h-screen flex flex-col font-sans text-neutral antialiased selection:bg-secondary selection:text-primary">
    
    <!-- Hero Section -->
    <div class="relative min-h-screen flex items-center justify-center">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="/images/hero.png" alt="Interior de barbería premium" class="w-full h-full object-cover opacity-30 mix-blend-overlay">
            <div class="absolute inset-0 bg-gradient-to-t from-primary via-primary/80 to-transparent"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 text-center px-6 max-w-4xl mx-auto flex flex-col items-center">
            
            <!-- Logo Emoji Hipster -->
            <div class="mb-6 animate-[fadeIn_1s_ease-out]">
                <div class="w-24 h-24 mx-auto bg-primary border-2 border-secondary rounded-full flex items-center justify-center shadow-[0_0_30px_rgba(197,160,89,0.3)]">
                    <span class="text-5xl">🧔🏻‍♂️💈</span>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-5xl md:text-7xl font-serif font-bold text-white mb-6 tracking-tight drop-shadow-xl">
                La Vieja Guardia
            </h1>
            
            <p class="text-secondary text-lg md:text-2xl font-medium mb-12 max-w-2xl mx-auto drop-shadow-md">
                Tradición, estilo y precisión. Reserva tu cita con los mejores profesionales en un ambiente exclusivo.
            </p>
            
            <!-- Actions -->
            <div class="space-x-0 space-y-4 sm:space-y-0 sm:space-x-6 flex flex-col sm:flex-row justify-center items-center w-full">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex justify-center items-center py-4 px-10 bg-secondary text-primary font-bold rounded-lg hover:bg-secondary/90 transition-all shadow-[0_4px_20px_rgba(197,160,89,0.4)] hover:shadow-[0_4px_25px_rgba(197,160,89,0.6)] hover:-translate-y-1">
                        Ir al Panel
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex justify-center items-center py-4 px-10 bg-secondary text-primary font-bold rounded-lg hover:bg-secondary/90 transition-all shadow-[0_4px_20px_rgba(197,160,89,0.4)] hover:shadow-[0_4px_25px_rgba(197,160,89,0.6)] hover:-translate-y-1">
                        Iniciar Sesión
                    </a>
                    <a href="#" class="w-full sm:w-auto inline-flex justify-center items-center py-4 px-10 bg-transparent border-2 border-secondary text-secondary font-bold rounded-lg hover:bg-secondary/10 transition-all">
                        Ver Servicios
                    </a>
                @endauth
            </div>
            
            <!-- Bottom decorative element -->
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex space-x-2">
                <div class="w-2 h-2 rounded-full bg-secondary opacity-50"></div>
                <div class="w-2 h-2 rounded-full bg-secondary opacity-100"></div>
                <div class="w-2 h-2 rounded-full bg-secondary opacity-50"></div>
            </div>
        </div>
    </div>
</body>
</html>

