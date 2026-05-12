<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Vieja Guardia - Inicio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center">
    <div class="text-center">
        <h1 class="text-5xl font-bold text-gray-800 mb-6">Bienvenido a la La Vieja Guardia</h1>
        <p class="text-gray-600 mb-8 text-xl">Reserva tus citas con los mejores profesionales.</p>
        
        <div class="space-x-4">
            @auth
                <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white font-bold py-2 px-6 rounded hover:bg-blue-700">Ir al Panel</a>
            @else
                <a href="{{ route('login') }}" class="bg-blue-600 text-white font-bold py-2 px-6 rounded hover:bg-blue-700">Iniciar Sesión</a>
            @endauth
        </div>
    </div>
</body>
</html>

