@extends('layouts.app_admin')

@section('content')
<div class="">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Lista de Clientes</h2>
            <a href="{{ route('secretaria.clientes.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded font-bold hover:bg-blue-600 shadow flex items-center">
                + Agregar Cliente
            </a>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 border-t-4 border-blue-500 overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="p-3">Nombre</th>
                        <th class="p-3">Correo</th>
                        <th class="p-3">Teléfono</th>
                        <th class="p-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-semibold">{{ $cliente->nombre }}</td>
                        <td class="p-3">{{ $cliente->correo ?? '-' }}</td>
                        <td class="p-3">{{ $cliente->telefono ?? '-' }}</td>
                        <td class="p-3 text-center">
                            <a href="{{ route('secretaria.clientes.edit', $cliente->id) }}" class="bg-yellow-500 text-white px-3 py-1 rounded text-xs hover:bg-yellow-600 mr-2">Editar</a>
                            <form action="{{ route('secretaria.clientes.destroy', $cliente->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro que deseas eliminar este cliente?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-gray-500 text-lg">No hay clientes registrados en esta sucursal.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $clientes->links() }}
        </div>
    </div>
@endsection
