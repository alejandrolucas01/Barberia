@extends('layouts.app_admin')

@section('content')
<div class="">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 mb-8">
            <h2 class="text-3xl font-bold mb-2">{{ $sucursal->nombre }}</h2>
            <p class="text-gray-600"><strong>Dirección:</strong> {{ $sucursal->direccion ?? 'No especificada' }}</p>
            <p class="text-gray-600"><strong>Teléfono:</strong> {{ $sucursal->telefono ?? 'No especificado' }}</p>
            <p class="text-gray-600"><strong>Días de apertura:</strong> {{ $sucursal->dias_apertura ? implode(', ', $sucursal->dias_apertura) : 'No especificado' }}</p>
            <p class="text-gray-600"><strong>Horario:</strong> 
                @if($sucursal->hora_apertura && $sucursal->hora_cierre)
                    De {{ \Carbon\Carbon::parse($sucursal->hora_apertura)->format('h:i A') }} a {{ \Carbon\Carbon::parse($sucursal->hora_cierre)->format('h:i A') }}
                @else
                    No especificado
                @endif
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Secretarias -->
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
                <h3 class="text-2xl font-semibold tracking-tight text-slate-900 mb-4 border-b pb-2">Secretarias Asignadas ({{ $sucursal->secretarias->count() }}/2)</h3>
                @if($sucursal->secretarias->isEmpty())
                    <p class="text-gray-500 italic">No hay secretarias registradas en esta sucursal.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($sucursal->secretarias as $secretaria)
                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $secretaria->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $secretaria->email }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.secretarias.edit', $secretaria->id) }}" class="text-sm py-1.5 px-3 text-center bg-white border border-slate-200 text-slate-700 text-xs font-medium rounded-md hover:bg-slate-50 transition-colors">Editar</a>
                                    <form action="{{ route('admin.secretarias.destroy', $secretaria->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta secretaria?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm py-1.5 px-3 text-center bg-white border border-slate-200 text-rose-600 text-xs font-medium rounded-md hover:bg-rose-50 transition-colors">Eliminar</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- Barberos -->
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
                <h3 class="text-2xl font-semibold tracking-tight text-slate-900 mb-4 border-b pb-2">Barberos Registrados</h3>
                @if($sucursal->barberos->isEmpty())
                    <p class="text-gray-500 italic">No hay barberos registrados en esta sucursal.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($sucursal->barberos as $barbero)
                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $barbero->nombre }}</p>
                                    <p class="text-sm text-gray-500">Tel: {{ $barbero->telefono ?? 'N/A' }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <form action="{{ route('admin.barberos.destroy', $barbero->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este barbero?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm py-1.5 px-3 text-center bg-white border border-slate-200 text-rose-600 text-xs font-medium rounded-md hover:bg-rose-50 transition-colors">Eliminar</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
