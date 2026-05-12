@extends('layouts.app_admin')

@section('content')
<div class="max-w-2xl mx-auto">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <h2 class="text-2xl font-semibold tracking-tight text-slate-900 mb-6">Crear Nueva Sucursal</h2>
            
            <form action="{{ route('admin.sucursales.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre de la Sucursal</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors" required>
                    @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Dirección (Opcional)</label>
                    <input type="text" name="direccion" value="{{ old('direccion') }}" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Teléfono (Opcional)</label>
                    <input type="text" name="telefono" pattern="[0-9]{10}" maxlength="10" title="Debe contener exactamente 10 d�gitos num�ricos" value="{{ old('telefono') }}" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Horario de Atención (Opcional)</label>
                    <div class="bg-gray-50 p-4 border rounded">
                        <label class="block text-sm font-semibold mb-2 text-gray-600">Días de la semana (Calendario)</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-4">
                            @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="dias_apertura[]" value="{{ $dia }}" class="form-checkbox text-blue-600" {{ (is_array(old('dias_apertura')) && in_array($dia, old('dias_apertura'))) ? 'checked' : '' }}>
                                    <span class="ml-2">{{ $dia }}</span>
                                </label>
                            @endforeach
                        </div>
                        
                        <div class="flex space-x-4">
                            <div class="flex-1">
                                <label class="block text-sm font-semibold mb-2 text-gray-600">Hora Apertura (Reloj)</label>
                                <input type="time" name="hora_apertura" value="{{ old('hora_apertura') }}" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors">
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-semibold mb-2 text-gray-600">Hora Cierre (Reloj)</label>
                                <input type="time" name="hora_cierre" value="{{ old('hora_cierre') }}" class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400 transition-colors">
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">
                    Guardar Sucursal
                </button>
            </form>
        </div>
    </div>
@endsection
