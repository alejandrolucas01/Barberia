@extends('layouts.app_admin')

@section('content')
<div class="max-w-2xl mx-auto">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <h2 class="text-2xl font-semibold tracking-tight text-slate-900 mb-6">Añadir Nuevo Servicio</h2>
            
            <form action="{{ route('admin.servicios.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre del Servicio</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors" required>
                    @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tiempo de Servicio (minutos)</label>
                    <input type="number" name="duracion_minutos" value="{{ old('duracion_minutos', 60) }}" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors" required>
                    @error('duracion_minutos') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Precio ($)</label>
                    <input type="number" step="0.01" name="precio" value="{{ old('precio') }}" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors" required>
                    @error('precio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <button type="submit" class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600">
                    Guardar Servicio
                </button>
            </form>
        </div>
    </div>
@endsection
