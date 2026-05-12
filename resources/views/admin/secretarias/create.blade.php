@extends('layouts.app_admin')

@section('content')
<div class="max-w-2xl mx-auto">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <h2 class="text-2xl font-semibold tracking-tight text-slate-900 mb-6">Registrar Secretaria</h2>
            
            @if($errors->any())
                <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.secretarias.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre Completo</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Correo Electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Asignar a Sucursal (Máx 2 secretarias por sucursal)</label>
                    <select name="sucursal_id" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors" required>
                        <option value="">Seleccione una sucursal</option>
                        @foreach($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}" {{ old('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                                {{ $sucursal->nombre }} - {{ $sucursal->direccion ?? 'Sin dirección' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Contraseña</label>
                    <input type="password" name="password" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors" required minlength="6">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors" required minlength="6">
                </div>
                
                <button type="submit" class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600">
                    Registrar Secretaria
                </button>
            </form>
        </div>
    </div>
@endsection
