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
                
                <div class="mb-6 relative">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">1. Buscar y Seleccionar Cliente <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <!-- Input for Search -->
                        <div class="relative flex items-center">
                            <span class="absolute left-3 text-slate-400">🔍</span>
                            <input type="text" id="cliente_search_input" placeholder="Escribe para buscar un cliente por nombre o teléfono..." class="w-full border border-slate-200 pl-10 pr-10 py-3 rounded-xl bg-white focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all shadow-sm" required autocomplete="off">
                            <button type="button" id="clear_cliente" class="absolute right-3 text-slate-400 hover:text-slate-600 hidden font-bold">✕</button>
                        </div>
                        
                        <!-- Hidden Input for actual value -->
                        <input type="hidden" name="cliente_id" id="cliente_id_hidden" required>
                        
                        <!-- Autocomplete Dropdown List -->
                        <div id="autocomplete_dropdown" class="absolute left-0 right-0 mt-1 max-h-72 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-xl hidden z-50 transition-all duration-200">
                            <!-- Local branch clients -->
                            <div id="local_clients_header" class="px-3 py-2 text-xs font-bold text-slate-500 bg-slate-50 border-b border-slate-100 flex items-center gap-1.5">
                                <span>📍</span> Clientes de esta Sucursal
                            </div>
                            <div id="local_clients_list">
                                @foreach($clientesLocal as $cliente)
                                    <div class="cliente-option px-4 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-50 flex justify-between items-center transition-colors" data-id="{{ $cliente->id }}" data-search="{{ strtolower($cliente->nombre . ' ' . $cliente->telefono) }}" data-name="{{ $cliente->nombre }}" data-phone="{{ $cliente->telefono }}">
                                        <div class="font-medium text-slate-800">{{ $cliente->nombre }}</div>
                                        @if($cliente->telefono)
                                            <div class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-0.5 rounded border border-slate-200">📞 {{ $cliente->telefono }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <!-- Other branch clients -->
                            <div id="other_clients_header" class="px-3 py-2 text-xs font-bold text-slate-500 bg-slate-50 border-b border-slate-100 border-t flex items-center gap-1.5">
                                <span>🌐</span> Clientes de otras Sucursales
                            </div>
                            <div id="other_clients_list">
                                @foreach($clientesOtros as $cliente)
                                    <div class="cliente-option px-4 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-50 flex justify-between items-center transition-colors" data-id="{{ $cliente->id }}" data-search="{{ strtolower($cliente->nombre . ' ' . $cliente->telefono) }}" data-name="{{ $cliente->nombre }}" data-phone="{{ $cliente->telefono }}">
                                        <div class="font-medium text-slate-800">{{ $cliente->nombre }}</div>
                                        @if($cliente->telefono)
                                            <div class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-0.5 rounded border border-slate-200">📞 {{ $cliente->telefono }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- No results helper -->
                            <div id="no_results" class="px-4 py-6 text-center text-slate-500 text-sm hidden">
                                ❌ No se encontraron clientes
                            </div>
                        </div>
                    </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('cliente_search_input');
            const idHidden = document.getElementById('cliente_id_hidden');
            const dropdown = document.getElementById('autocomplete_dropdown');
            const clearBtn = document.getElementById('clear_cliente');
            const options = document.querySelectorAll('.cliente-option');
            const noResults = document.getElementById('no_results');

            const localHeader = document.getElementById('local_clients_header');
            const otherHeader = document.getElementById('other_clients_header');

            // Show dropdown on focus
            searchInput.addEventListener('focus', function() {
                filterOptions();
                dropdown.classList.remove('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });

            // Filter on input
            searchInput.addEventListener('input', function() {
                filterOptions();
                dropdown.classList.remove('hidden');
                if (searchInput.value) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                    idHidden.value = '';
                }
            });

            // Clear search
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                idHidden.value = '';
                clearBtn.classList.add('hidden');
                filterOptions();
                dropdown.classList.add('hidden');
            });

            // Option selection
            options.forEach(option => {
                option.addEventListener('click', function() {
                    selectOption(option);
                });
            });

            function selectOption(option) {
                const name = option.dataset.name;
                const phone = option.dataset.phone;
                const id = option.dataset.id;

                searchInput.value = name + (phone ? ' (' + phone + ')' : '');
                idHidden.value = id;
                clearBtn.classList.remove('hidden');
                dropdown.classList.add('hidden');
            }

            function filterOptions() {
                const term = searchInput.value.toLowerCase().trim();
                let visibleLocal = 0;
                let visibleOther = 0;

                options.forEach(option => {
                    const searchData = option.dataset.search;
                    const isLocal = option.parentElement.id === 'local_clients_list';
                    
                    if (searchData.includes(term)) {
                        option.classList.remove('hidden');
                        if (isLocal) visibleLocal++;
                        else visibleOther++;
                    } else {
                        option.classList.add('hidden');
                    }
                });

                // Toggle headers based on contents
                if (visibleLocal > 0) {
                    localHeader.classList.remove('hidden');
                } else {
                    localHeader.classList.add('hidden');
                }

                if (visibleOther > 0) {
                    otherHeader.classList.remove('hidden');
                } else {
                    otherHeader.classList.add('hidden');
                }

                // Show no results helper
                if (visibleLocal === 0 && visibleOther === 0) {
                    noResults.classList.remove('hidden');
                } else {
                    noResults.classList.add('hidden');
                }
            }
        });
    </script>
@endsection
