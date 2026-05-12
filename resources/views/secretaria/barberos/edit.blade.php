@extends('layouts.app_admin')

@section('content')
<div class="max-w-2xl mx-auto">
        <div class="bg-white p-8 rounded-lg shadow-lg border-t-4 border-gray-700">
            <h2 class="text-2xl font-semibold tracking-tight text-slate-900 mb-6 text-gray-800 border-b pb-2">Editar Datos de {{ $barbero->nombre }}</h2>
            
            <form action="{{ route('secretaria.barberos.update', $barbero->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre Completo <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $barbero->nombre) }}" class="w-full px-3 py-3 border rounded bg-gray-50 focus:outline-none focus:border-gray-700" required>
                    @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tel√©fono</label>
                    <input type="text" name="telefono" pattern="[0-9]{10}" maxlength="10" title="Debe contener exactamente 10 dÌgitos numÈricos" value="{{ old('telefono', $barbero->telefono) }}" class="w-full px-3 py-3 border rounded bg-gray-50 focus:outline-none focus:border-gray-700">
                    @error('telefono') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Correo Electr√≥nico</label>
                    <input type="email" name="correo" value="{{ old('correo', $barbero->correo) }}" class="w-full px-3 py-3 border rounded bg-gray-50 focus:outline-none focus:border-gray-700">
                    @error('correo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Hora de Entrada <span class="text-red-500">*</span></label>
                        <input type="time" name="hora_entrada" value="{{ old('hora_entrada', \Carbon\Carbon::parse($barbero->hora_entrada)->format('H:i')) }}" class="w-full px-3 py-3 border rounded bg-gray-50 focus:outline-none focus:border-gray-700" required>
                        @error('hora_entrada') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Hora de Salida <span class="text-red-500">*</span></label>
                        <input type="time" name="hora_salida" value="{{ old('hora_salida', \Carbon\Carbon::parse($barbero->hora_salida)->format('H:i')) }}" class="w-full px-3 py-3 border rounded bg-gray-50 focus:outline-none focus:border-gray-700" required>
                        @error('hora_salida') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-yellow-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-yellow-600 text-lg shadow-md transition">
                    Actualizar Barbero
                </button>
            </form>
        </div>
    </div>
@endsection
