@extends('layouts.app_admin')

@section('content')
    <!-- Encabezado de la Sección -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold tracking-tight text-slate-900">Módulo de Reportes y Rentabilidad</h2>
        <p class="text-slate-500 mt-1">Genera reportes de ventas consolidados o filtra el rendimiento específico por
            sucursal.</p>
    </div>

    <!-- Contenedor Principal de Reportes -->
    <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm mb-12">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8 pb-6 border-b border-slate-100">
            <div>
                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Análisis de Ingresos Globales</h3>
                <p class="text-sm text-slate-500 mt-1">Comparativa de ingresos aportados por cada sucursal en tiempo real.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Total:</span>
                    <span class="text-lg font-black text-slate-900">${{ number_format($totalVentasGeneral ?? 0, 2) }}</span>
                </div>
                <div class="flex items-center gap-2 bg-emerald-50 p-3 rounded-xl border border-emerald-200">
                    <span class="text-xs font-semibold text-emerald-800 uppercase tracking-wider">Efectivo:</span>
                    <span class="text-lg font-black text-emerald-700">${{ number_format($totalEfectivoGeneral ?? 0, 2) }}</span>
                </div>
                <div class="flex items-center gap-2 bg-amber-50 p-3 rounded-xl border border-amber-200">
                    <span class="text-xs font-semibold text-amber-800 uppercase tracking-wider">Tarjeta:</span>
                    <span class="text-lg font-black text-amber-700">${{ number_format($totalTarjetaGeneral ?? 0, 2) }}</span>
                </div>
                <div class="flex items-center gap-2 bg-rose-50 p-3 rounded-xl border border-rose-200">
                    <span class="text-xs font-semibold text-rose-800 uppercase tracking-wider">Cancelados:</span>
                    <span class="text-lg font-black text-rose-600">{{ $totalCanceladasGeneral ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <!-- Columna Izquierda: Controles y Generación de PDF -->
            <div class="space-y-6 bg-slate-50/70 p-6 rounded-xl border border-slate-100">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Opciones de Descarga</h4>

                <!-- 1. Reporte General -->
                <div>
                    <a href="{{ route('admin.reportes.general') }}"
                        class="w-full bg-slate-900 text-white font-bold text-sm px-4 py-3 rounded-xl shadow hover:bg-slate-800 transition-all flex items-center justify-center gap-2">
                        <span>📥</span> Reporte General (Todas)
                    </a>
                    <p class="text-[11px] text-slate-400 mt-1.5 text-center">Descarga un PDF con el consolidado global</p>
                </div>

                <hr class="border-slate-200">

                <!-- 2. Filtro de Vista por Sucursal -->
                <form action="{{ route('admin.reportes.index') }}" method="GET" class="space-y-3" id="formFiltroSucursal">
                    <label class="block text-xs font-semibold text-slate-700">Filtrar vista por Sucursal:</label>
                    <div class="flex gap-2">
                        <select name="sucursal_id"
                            class="w-full bg-white border border-slate-300 text-slate-800 rounded-lg text-xs p-2.5 focus:ring-2 focus:ring-slate-900 focus:outline-none"
                            onchange="document.getElementById('formFiltroSucursal').submit()">
                            <option value="">Todas las sucursales</option>
                            @foreach ($todasSucursales as $suc)
                                <option value="{{ $suc->id }}"
                                    {{ ($sucursalFilterId ?? '') == $suc->id ? 'selected' : '' }}>
                                    {{ $suc->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @if ($sucursalFilterId)
                            <a href="{{ route('admin.reportes.index') }}"
                                class="px-2.5 py-2 text-xs bg-white border border-slate-200 text-slate-500 hover:text-slate-900 rounded-lg flex items-center"
                                title="Limpiar filtro">
                                ✖
                            </a>
                        @endif
                    </div>
                </form>

                <!-- 3. Descarga de Reporte Individual -->
                <div>
                    @php
                        $sucursalSeleccionadaId = $sucursalFilterId ?: $todasSucursales->first()->id ?? 0;
                    @endphp
                    <a href="{{ route('admin.reportes.sucursal', ['sucursal_id' => $sucursalSeleccionadaId]) }}"
                        id="btnReporteSucursal"
                        class="w-full bg-white border border-slate-300 text-slate-800 font-bold text-sm px-4 py-3 rounded-xl shadow-sm hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
                        <span>📄</span> Reporte por Sucursal
                    </a>
                    <p class="text-[11px] text-slate-400 mt-1.5 text-center">Aplica a la sucursal seleccionada en el filtro
                    </p>
                </div>
            </div>

            <!-- Columna Derecha: Gráfica de Rentabilidad -->
            <div class="lg:col-span-2">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Comparativa de Ventas Totales por
                    Sucursal</h4>
                @if (empty($chartData) || array_sum($chartData) == 0)
                    <div class="py-12 text-center text-slate-400 italic bg-slate-50/30 rounded-xl border border-slate-100">
                        <span class="text-3xl block mb-2">📉</span>
                        Aún no hay ventas finalizadas para graficar rentabilidad.
                    </div>
                @else
                    <div class="relative w-full h-[320px]">
                        <canvas id="rentabilidadChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if (!empty($chartData) && array_sum($chartData) > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('rentabilidadChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            label: 'Ventas Totales ($)',
                            data: @json($chartData),
                            backgroundColor: '#8B5E3C', // Beige/Oro Premium
                            hoverBackgroundColor: '#704B30',
                            borderRadius: 8,
                            maxBarThickness: 50
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'x', // Barras Verticales para comparación directa
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let value = context.raw || 0;
                                        return ' Rentabilidad: $' + parseFloat(value).toFixed(2);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value;
                                    },
                                    font: {
                                        family: 'Outfit',
                                        size: 12
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        family: 'Outfit',
                                        weight: 'bold',
                                        size: 13
                                    }
                                }
                            }
                        }
                    }
                });

                // Sincronizar dinámicamente el botón de descarga por sucursal
                const selectSucursal = document.querySelector('select[name="sucursal_id"]');
                const btnReporteSucursal = document.getElementById('btnReporteSucursal');
                if (selectSucursal && btnReporteSucursal) {
                    selectSucursal.addEventListener('change', function() {
                        let sucId = this.value || @json($todasSucursales->first()->id ?? 0);
                        let baseUrl = "{{ route('admin.reportes.sucursal') }}";
                        btnReporteSucursal.href = baseUrl + "?sucursal_id=" + sucId;
                    });
                }
            });
        </script>
    @endif
@endsection
