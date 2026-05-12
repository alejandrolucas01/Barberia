<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - La Vieja Guardia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#FAFAFA] flex items-center justify-center min-h-screen text-slate-900 antialiased selection:bg-slate-200">
    <div class="w-full max-w-[400px] px-6">
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-semibold tracking-tight">La Vieja Guardia</h2>
            <p class="text-slate-500 text-sm mt-2">Acceso al panel de administración</p>
        </div>
        
        <div class="bg-white rounded-2xl shadow-[0_0_0_1px_rgba(0,0,0,0.05),0_2px_10px_0_rgba(0,0,0,0.02)] p-8">
            @if (session('status'))
                <div class="mb-6 text-sm font-medium text-emerald-600 bg-emerald-50/50 px-4 py-3 rounded-lg border border-emerald-100">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 text-sm font-medium text-rose-600 bg-rose-50/50 px-4 py-3 rounded-lg border border-rose-100">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-slate-700">Correo Electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors">
                </div>

                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-slate-700">Contraseña</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors">
                </div>

                <button type="submit" class="w-full flex justify-center py-2.5 px-4 mt-2 border border-transparent rounded-lg text-sm font-medium text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 transition-all active:scale-[0.98]">
                    Ingresar
                </button>
            </form>
        </div>
    </div>
</body>
</html>

