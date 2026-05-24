@extends('layouts.app_admin')

@section('content')
<div class=" max-w-2xl">
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 shadow-sm">
                <strong class="font-bold">Error:</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

                <div class="mb-8">
            <h2 class="text-2xl font-semibold tracking-tight text-slate-900 mb-6 text-gray-800 border-b pb-2">Detalles de la {{ $tipo == 'cita' ? 'Cita' : 'Venta' }}</h2>
            <form action="{{ route('secretaria.ventas.store') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">1. Buscar y Seleccionar Cliente <span class="text-red-500">*</span></label>
                    <select name="cliente_id" id="cliente_id" class="w-full border p-2 rounded select2" required>
                        <option value="">Escribe para buscar un cliente en el sistema...</option>
                        <optgroup label="📍 Clientes de esta Sucursal">
                            @foreach($clientesLocal as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }} {{ $cliente->telefono ? '('.$cliente->telefono.')' : '' }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="🌐 Clientes de otras Sucursales / Generales">
                            @foreach($clientesOtros as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }} {{ $cliente->telefono ? '('.$cliente->telefono.')' : '' }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">2. Servicios Solicitados <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 gap-3 border p-4 rounded-lg bg-gray-50 shadow-inner">
                        @foreach($servicios as $servicio)
                            <label class="flex items-center p-2 hover:bg-gray-200 rounded cursor-pointer transition">
                                <input type="checkbox" name="servicios[]" value="{{ $servicio->id }}" class="form-checkbox h-5 w-5 text-green-600">
                                <span class="ml-3 font-semibold">{{ $servicio->nombre }}</span>
                                <span class="ml-auto text-gray-600 font-mono">${{ number_format($servicio->precio, 2) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">3. Fecha <span class="text-red-500">*</span></label>
                        <input type="date" name="fecha" value="{{ date('Y-m-d') }}" class="w-full border p-3 rounded bg-gray-50" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Hora de Inicio <span class="text-red-500">*</span></label>
                        <input type="time" name="hora_inicio" value="{{ $tipo == 'venta' ? date('H:i') : '' }}" class="w-full border p-3 rounded bg-gray-50" required>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-gray-700 font-bold mb-3">4. Tipo de Atención <span class="text-red-500">*</span></label>
                    <div class="flex space-x-6 border p-4 rounded-lg bg-gray-50">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="tipo_atencion" value="con_cita" class="form-radio h-5 w-5 text-green-600" {{ $tipo == 'cita' ? 'checked' : '' }}>
                            <span class="ml-2 font-semibold">Con Cita (Agendado)</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="tipo_atencion" value="sin_cita" class="form-radio h-5 w-5 text-green-600" {{ $tipo == 'venta' ? 'checked' : '' }}>
                            <span class="ml-2 font-semibold">Sin Cita (Venta Inmediata)</span>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-green-500 text-white font-bold py-4 px-4 rounded-lg hover:bg-green-600 text-xl shadow-lg transition duration-200 flex justify-center items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Aceptar y Asignar Barbero
                </button>
                <p class="text-center text-sm text-gray-500 mt-4">* El sistema buscará automáticamente un barbero con horario libre en esta sucursal.</p>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Buscar por nombre de cliente...",
                width: '100%'
            });
        });
    </script>
@endsection
