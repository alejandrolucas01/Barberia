@extends('layouts.app_admin')

@section('content')
<div class="max-w-2xl mx-auto">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <h2 class="text-2xl font-semibold tracking-tight text-slate-900 mb-6">Editar Sucursal</h2>
            
            <form action="{{ route('admin.sucursales.update', $sucursal->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre de la Sucursal</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $sucursal->nombre) }}" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors" required>
                    @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Dirección (Opcional)</label>
                    <input type="text" name="direccion" value="{{ old('direccion', $sucursal->direccion) }}" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Teléfono (Opcional)</label>
                    <input type="text" name="telefono" pattern="[0-9]{10}" maxlength="10" title="Debe contener exactamente 10 d�gitos num�ricos" value="{{ old('telefono', $sucursal->telefono) }}" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Horario de Atención (Opcional)</label>
                    <div class="bg-gray-50 p-4 border rounded">
                        <label class="block text-sm font-semibold mb-2 text-gray-600">Días de la semana (Calendario)</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-4">
                            @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="dias_apertura[]" value="{{ $dia }}" class="form-checkbox text-blue-600" {{ (is_array(old('dias_apertura', $sucursal->dias_apertura ?? [])) && in_array($dia, old('dias_apertura', $sucursal->dias_apertura ?? []))) ? 'checked' : '' }}>
                                    <span class="ml-2">{{ $dia }}</span>
                                </label>
                            @endforeach
                        </div>
                        
                        <div class="flex space-x-4">
                            <div class="flex-1">
                                <label class="block text-sm font-semibold mb-2 text-gray-600">Hora Apertura (Reloj)</label>
                                <input type="time" name="hora_apertura" value="{{ old('hora_apertura', $sucursal->hora_apertura ? \Carbon\Carbon::parse($sucursal->hora_apertura)->format('H:i') : '') }}" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors">
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-semibold mb-2 text-gray-600">Hora Cierre (Reloj)</label>
                                <input type="time" name="hora_cierre" value="{{ old('hora_cierre', $sucursal->hora_cierre ? \Carbon\Carbon::parse($sucursal->hora_cierre)->format('H:i') : '') }}" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors">
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-yellow-500 text-white font-bold py-2 px-4 rounded hover:bg-yellow-600 transition">
                    Actualizar Sucursal
                </button>
            </form>
        </div>
    </div>
@endsection
