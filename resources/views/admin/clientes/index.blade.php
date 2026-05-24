@extends('layouts.app_admin')

@section('content')
<div class="">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 mb-8 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold mb-2">Base de Datos de Clientes</h2>
                <p class="text-gray-600">Administra los clientes (como si fueran barberos, no tienen inicio de sesión).</p>
            </div>
            <a href="{{ route('admin.clientes.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Añadir Cliente</a>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            @if($clientes->isEmpty())
                <p class="text-gray-500 italic">No hay clientes registrados en el sistema.</p>
            @else
                <table class="w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border p-2 text-left">ID Cliente</th>
                            <th class="border p-2 text-left">Nombre</th>
                            <th class="border p-2 text-left">Teléfono</th>
                            <th class="border p-2 text-left">Correo</th>
                            <th class="border p-2 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientes as $cliente)
                        <tr class="hover:bg-gray-50">
                            <td class="border p-2">{{ $cliente->id }}</td>
                            <td class="border p-2">{{ $cliente->nombre }}</td>
                            <td class="border p-2">{{ $cliente->telefono ?? 'N/A' }}</td>
                            <td class="border p-2">{{ $cliente->correo ?? 'N/A' }}</td>
                            <td class="border p-2 text-center">
                                <form action="{{ route('admin.clientes.destroy', $cliente->id) }}" method="POST" class="inline form-eliminar" data-title="¿Eliminar cliente?" data-warning="Esta acción no se puede deshacer.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm py-1.5 px-3 text-center bg-white border border-slate-200 text-rose-600 text-xs font-medium rounded-md hover:bg-rose-50 transition-colors">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $clientes->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
