@extends('layouts.app_admin')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Historial de Ventas y Atenciones</h2>
                <p class="text-slate-500 text-sm">Resumen en vivo de la productividad del día actual.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div
                    class="flex items-center gap-2 bg-slate-50 text-slate-800 px-4 py-3 rounded-xl border border-slate-200 font-bold text-sm">
                    <span>💰 Total:</span>
                    <span class="text-base">${{ number_format($totalVentas ?? 0, 2) }}</span>
                </div>
                <div
                    class="flex items-center gap-2 bg-emerald-50 text-emerald-800 px-4 py-3 rounded-xl border border-emerald-200 font-bold text-sm">
                    <span>💵 Efectivo:</span>
                    <span class="text-base">${{ number_format($totalEfectivo ?? 0, 2) }}</span>
                </div>
                <div
                    class="flex items-center gap-2 bg-blue-50 text-blue-800 px-4 py-3 rounded-xl border border-blue-200 font-bold text-sm">
                    <span>💳 Tarjeta:</span>
                    <span class="text-base">${{ number_format($totalTarjeta ?? 0, 2) }}</span>
                </div>
                <a href="{{ route('secretaria.ventas.export') }}"
                    class="bg-white border border-slate-200 text-slate-700 px-4 py-3 rounded-xl shadow-sm font-bold text-sm hover:bg-slate-50 transition-all flex items-center gap-2">
                    <span>📥</span> Descargar PDF
                </a>
            </div>
        </div>

        <!-- 1. Tarjeta de Tabla de Historial (Arriba) -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 overflow-x-auto">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Registro Detallado</h3>
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 uppercase text-xs tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-3">Hora</th>
                        <th class="p-3">Cliente</th>
                        <th class="p-3">Servicio</th>
                        <th class="p-3">Barbero</th>
                        <th class="p-3">Tipo</th>
                        <th class="p-3">Precio</th>
                        <th class="p-3 text-center">Método</th>
                        <th class="p-3 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($ventas as $venta)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-3 font-semibold text-slate-900">
                                {{ \Carbon\Carbon::parse($venta->hora_inicio)->format('h:i A') }}</td>
                            <td class="p-3 text-slate-700">{{ $venta->cliente->nombre }}</td>
                            <td class="p-3 text-slate-700 font-medium">{{ $venta->servicio->nombre }}</td>
                            <td class="p-3 text-secondary font-bold">{{ $venta->barbero->nombre }}</td>
                            <td class="p-3">
                                <span
                                    class="px-2 py-0.5 text-xs rounded-full font-medium {{ $venta->tipo_atencion == 'con_cita' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-blue-50 text-blue-800 border border-blue-200/60' }}">
                                    {{ $venta->tipo_atencion == 'con_cita' ? 'Cita' : 'Walk-in' }}
                                </span>
                            </td>
                            <td class="p-3 font-bold text-slate-900">
                                ${{ $venta->estado == 'completada' ? number_format($venta->servicio->precio, 2) : '0.00' }}
                            </td>
                            <td class="p-3 text-center">
                                @if ($venta->estado == 'completada')
                                    <span
                                        class="px-2 py-0.5 text-xs rounded-full font-bold {{ $venta->metodo_pago == 'tarjeta' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                                        {{ ucfirst($venta->metodo_pago ?? 'Efectivo') }}
                                    </span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                <span
                                    class="px-2 py-0.5 text-xs rounded-full font-bold {{ $venta->estado == 'completada' ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}">
                                    {{ ucfirst($venta->estado) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-slate-400 italic">Aún no se ha completado ni
                                cancelado ninguna cita el día de hoy.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $ventas->links() }}
            </div>
        </div>

        <!-- Contenedor de Gráficas (Abajo) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Gráfica 1: Distribución de Servicios -->
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 w-full text-center">
                <h3 class="text-lg font-bold text-slate-800 mb-1">Distribución de Servicios</h3>
                <p class="text-xs text-slate-400 mb-6">Proporción de completados vs cancelados hoy</p>

                @if (empty($chartCounts))
                    <div class="py-8 text-slate-400 italic">
                        <span class="text-4xl block mb-2">📊</span>
                        Aún no hay atenciones para graficar hoy.
                    </div>
                @else
                    <div class="relative w-full max-w-[260px] mx-auto aspect-square mb-4">
                        <canvas id="serviciosChart"></canvas>
                    </div>
                @endif
            </div>

            <!-- Gráfica 2: Métodos de Pago -->
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 w-full text-center">
                <h3 class="text-lg font-bold text-slate-800 mb-1">Métodos de Cobro</h3>
                <p class="text-xs text-slate-400 mb-6">Transacciones liquidadas en Efectivo vs Tarjeta</p>

                @if (empty($metodoChartCounts) || array_sum($metodoChartCounts) == 0)
                    <div class="py-8 text-slate-400 italic">
                        <span class="text-4xl block mb-2">💳</span>
                        Aún no hay cobros realizados para graficar hoy.
                    </div>
                @else
                    <div class="relative w-full max-w-[260px] mx-auto aspect-square mb-4">
                        <canvas id="metodosChart"></canvas>
                    </div>
                @endif
            </div>
        </div>

        @if (!empty($chartCounts) || (!empty($metodoChartCounts) && array_sum($metodoChartCounts) > 0))
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    @if (!empty($chartCounts))
                        const ctxServicios = document.getElementById('serviciosChart').getContext('2d');
                        new Chart(ctxServicios, {
                            type: 'pie',
                            data: {
                                labels: @json($chartLabels),
                                datasets: [{
                                    data: @json($chartCounts),
                                    backgroundColor: @json($chartColors),
                                    borderWidth: 2,
                                    borderColor: '#ffffff'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            boxWidth: 12,
                                            padding: 10,
                                            font: {
                                                family: 'Outfit',
                                                size: 12
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    @endif

                    @if (!empty($metodoChartCounts) && array_sum($metodoChartCounts) > 0)
                        const ctxMetodos = document.getElementById('metodosChart').getContext('2d');
                        new Chart(ctxMetodos, {
                            type: 'doughnut',
                            data: {
                                labels: @json($metodoChartLabels),
                                datasets: [{
                                    data: @json($metodoChartCounts),
                                    backgroundColor: @json($metodoChartColors),
                                    borderWidth: 2,
                                    borderColor: '#ffffff'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            boxWidth: 12,
                                            padding: 10,
                                            font: {
                                                family: 'Outfit',
                                                size: 12
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    @endif
                });
            </script>
        @endif
    </div>
@endsection
