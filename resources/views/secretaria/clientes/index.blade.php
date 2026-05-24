@extends('layouts.app_admin')

@section('content')
    <div class="space-y-8">
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: '{{ session('success') }}',
                        confirmButtonColor: '#10b981',
                        showConfirmButton: false,
                        timer: 3000
                    });
                });
            </script>
        @endif

        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Directorio de Clientes</h2>
                <p class="text-slate-500 text-sm">Gestiona los clientes registrados en tu local y consulta los de otras
                    sucursales.</p>
            </div>
            <a href="{{ route('secretaria.clientes.create') }}"
                class="bg-secondary text-primary px-4 py-2.5 rounded-xl font-bold hover:bg-secondary/90 shadow-sm flex items-center transition-all text-white">
                + Agregar Cliente
            </a>
        </div>

        <!-- Clientes de Este Local -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 border-t-4 border-secondary overflow-x-auto">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Clientes de tu Local</h3>
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 uppercase text-xs tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-3">Nombre</th>
                        <th class="p-3">Correo</th>
                        <th class="p-3">Teléfono</th>
                        <th class="p-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($clientesLocal as $cliente)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-3 font-semibold text-slate-900">{{ $cliente->nombre }}</td>
                            <td class="p-3 text-slate-600">{{ $cliente->correo ?? '-' }}</td>
                            <td class="p-3 text-slate-600">{{ $cliente->telefono ?? '-' }}</td>
                            <td class="p-3 text-center space-x-2">
                                <a href="{{ route('secretaria.clientes.edit', $cliente->id) }}"
                                    class="inline-block bg-amber-500 text-white px-3 py-1 rounded-md text-xs hover:bg-amber-600 font-medium transition-colors">Editar</a>
                                <form action="{{ route('secretaria.clientes.destroy', $cliente->id) }}" method="POST"
                                    class="inline form-eliminar" data-title="¿Eliminar cliente?"
                                    data-warning="Esta acción no se puede deshacer.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-rose-500 text-white px-3 py-1 rounded-md text-xs hover:bg-rose-600 font-medium transition-colors">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-slate-400 italic">No hay clientes registrados en
                                tu local.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $clientesLocal->links() }}
            </div>
        </div>

        <!-- Clientes de Otros Locales -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 overflow-x-auto opacity-95">
            <h3 class="text-lg font-bold text-slate-800 mb-1">Clientes de Otros Locales</h3>
            <p class="text-xs text-slate-400 mb-4">Información de consulta. Solo editables desde su respectivo local.</p>
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 uppercase text-xs tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-3">Nombre</th>
                        <th class="p-3">Correo</th>
                        <th class="p-3">Teléfono</th>
                        <th class="p-3">Local Origen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($clientesOtros as $cliente)
                        <tr class="hover:bg-slate-50/50 transition-colors text-slate-500">
                            <td class="p-3 font-semibold text-slate-700">{{ $cliente->nombre }}</td>
                            <td class="p-3">{{ $cliente->correo ?? '-' }}</td>
                            <td class="p-3">{{ $cliente->telefono ?? '-' }}</td>
                            <td class="p-3 font-medium text-secondary">
                                <span class="px-2 py-0.5 bg-secondary/10 rounded-full text-xs border border-secondary/20">
                                    {{ $cliente->sucursal->nombre ?? 'Sin asignar' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-slate-400 italic">No hay clientes registrados en
                                otros locales.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $clientesOtros->links() }}
            </div>
        </div>
    </div>
@endsection
