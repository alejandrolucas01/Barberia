@extends('layouts.app_admin')

@section('content')
<div class="">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Historial de Hoy</h2>
            <div class="bg-green-100 text-green-800 px-6 py-3 rounded-lg shadow font-bold text-xl border border-green-300">
                Total del Día: ${{ number_format($totalVentas, 2) }}
            </div>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 border-t-4 border-indigo-500 overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="p-3">Hora</th>
                        <th class="p-3">Cliente</th>
                        <th class="p-3">Servicio</th>
                        <th class="p-3">Barbero</th>
                        <th class="p-3">Tipo</th>
                        <th class="p-3">Precio</th>
                        <th class="p-3">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-semibold">{{ \Carbon\Carbon::parse($venta->hora_inicio)->format('h:i A') }}</td>
                        <td class="p-3">{{ $venta->cliente->nombre }}</td>
                        <td class="p-3">{{ $venta->servicio->nombre }}</td>
                        <td class="p-3 text-indigo-700 font-semibold">{{ $venta->barbero->nombre }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs rounded {{ $venta->tipo_atencion == 'con_cita' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $venta->tipo_atencion == 'con_cita' ? 'Cita' : 'Walk-in' }}
                            </span>
                        </td>
                        <td class="p-3 font-bold text-gray-800">
                            ${{ $venta->estado == 'completada' ? number_format($venta->servicio->precio, 2) : '0.00' }}
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs rounded font-bold {{ $venta->estado == 'completada' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                {{ ucfirst($venta->estado) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-gray-500 text-lg">Aún no se ha completado ni cancelado ninguna cita el día de hoy.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $ventas->links() }}
        </div>
    </div>
@endsection
