@extends('layouts.app_admin')

@section('content')
<div class="max-w-2xl mx-auto">
        <div class="bg-white p-8 rounded-lg shadow-lg border-t-4 border-yellow-500">
            <h2 class="text-2xl font-semibold tracking-tight text-slate-900 mb-6 text-gray-800 border-b pb-2">Editar Cita de {{ $cita->cliente->nombre }}</h2>
            
            <form action="{{ route('secretaria.citas.update', $cita->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Servicio</label>
                    <input type="text" value="{{ $cita->servicio->nombre }} ({{ $cita->servicio->duracion_minutos }} min)" class="w-full px-3 py-3 border rounded bg-gray-200 cursor-not-allowed" disabled>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Barbero Asignado <span class="text-red-500">*</span></label>
                    <select name="barbero_id" class="w-full px-3 py-3 border rounded bg-gray-50 focus:outline-none focus:border-yellow-500" required>
                        @foreach($barberos as $barbero)
                            <option value="{{ $barbero->id }}" {{ $cita->barbero_id == $barbero->id ? 'selected' : '' }}>
                                {{ $barbero->nombre }} ({{ \Carbon\Carbon::parse($barbero->hora_entrada)->format('H:i') }} - {{ \Carbon\Carbon::parse($barbero->hora_salida)->format('H:i') }})
                            </option>
                        @endforeach
                    </select>
                    @error('barbero_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Fecha <span class="text-red-500">*</span></label>
                    <input type="date" name="fecha" value="{{ old('fecha', $cita->fecha) }}" class="w-full px-3 py-3 border rounded bg-gray-50 focus:outline-none focus:border-yellow-500" required>
                    @error('fecha') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-8">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Hora de Inicio <span class="text-red-500">*</span></label>
                    <input type="time" name="hora_inicio" value="{{ old('hora_inicio', \Carbon\Carbon::parse($cita->hora_inicio)->format('H:i')) }}" class="w-full px-3 py-3 border rounded bg-gray-50 focus:outline-none focus:border-yellow-500" required>
                    @error('hora_inicio') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <button type="submit" class="w-full bg-yellow-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-yellow-600 text-lg shadow-md transition">
                    Guardar Cambios
                </button>
            </form>
        </div>
    </div>
@endsection
