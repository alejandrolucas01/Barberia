@extends('layouts.app_admin')

@section('content')
<div class="">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Lista de Barberos</h2>
            <a href="{{ route('secretaria.barberos.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded font-bold hover:bg-pink-700 shadow flex items-center">
                + Agregar Barbero
            </a>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 border-t-4 border-blue-500 overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="p-3">Nombre</th>
                        <th class="p-3">Correo</th>
                        <th class="p-3">Teléfono</th>
                        <th class="p-3">Horario</th>
                        <th class="p-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barberos as $barbero)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-semibold">{{ $barbero->nombre }}</td>
                        <td class="p-3">{{ $barbero->correo ?? '-' }}</td>
                        <td class="p-3">{{ $barbero->telefono ?? '-' }}</td>
                        <td class="p-3">
                            {{ $barbero->hora_entrada ? \Carbon\Carbon::parse($barbero->hora_entrada)->format('h:i A') : 'N/A' }} - 
                            {{ $barbero->hora_salida ? \Carbon\Carbon::parse($barbero->hora_salida)->format('h:i A') : 'N/A' }}
                        </td>
                        <td class="p-3 text-center">
                            <a href="{{ route('secretaria.barberos.edit', $barbero->id) }}" class="bg-yellow-500 text-white px-3 py-1 rounded text-xs hover:bg-yellow-600 mr-2">Editar</a>
                            <form action="{{ route('secretaria.barberos.destroy', $barbero->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro que deseas eliminar este barbero?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500 text-lg">No hay barberos registrados en esta sucursal.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $barberos->links() }}
        </div>
    </div>
@endsection
