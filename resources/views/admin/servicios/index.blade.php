@extends('layouts.app_admin')

@section('content')
    <div class="">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 mb-8 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold mb-2">Servicios Ofrecidos</h2>
                <p class="text-gray-600">Lista de servicios (Crea, edita o elimina servicios).</p>
            </div>
            <a href="{{ route('admin.servicios.create') }}"
                class="bg-blue-500 text-white px-4 py-2 rounded font-bold hover:bg-blue-600">Añadir Servicio</a>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            @if ($servicios->isEmpty())
                <p class="text-gray-500 italic">No hay servicios registrados.</p>
            @else
                <table class="w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border p-2 text-left">ID</th>
                            <th class="border p-2 text-left">Nombre del Servicio</th>
                            <th class="border p-2 text-left">Duración (min)</th>
                            <th class="border p-2 text-left">Precio</th>
                            <th class="border p-2 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($servicios as $servicio)
                            <tr class="hover:bg-gray-50">
                                <td class="border p-2">{{ $servicio->id }}</td>
                                <td class="border p-2">{{ $servicio->nombre }}</td>
                                <td class="border p-2">{{ $servicio->duracion_minutos }} min</td>
                                <td class="border p-2">${{ number_format($servicio->precio, 2) }}</td>
                                <td class="border p-2 text-center">
                                    <a href="{{ route('admin.servicios.edit', $servicio->id) }}"
                                        class="text-sm py-1.5 px-3 text-center bg-white border border-slate-200 text-slate-700 text-xs font-medium rounded-md hover:bg-slate-50 transition-colors mr-2">Editar</a>
                                    <form action="{{ route('admin.servicios.destroy', $servicio->id) }}" method="POST"
                                        onsubmit="return confirm('¿Eliminar servicio?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-sm py-1.5 px-3 text-center bg-white border border-slate-200 text-rose-600 text-xs font-medium rounded-md hover:bg-rose-50 transition-colors">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
