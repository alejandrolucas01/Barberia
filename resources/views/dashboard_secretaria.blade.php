@extends('layouts.app_admin')

@section('content')
    <div class="">
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

        <div class="mb-6">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Bienvenida, {{ auth()->user()->name }}</h2>
            <p class="text-gray-600">Desde aquí puedes gestionar las atenciones y revisar la agenda del día.</p>
        </div>

        <!-- Botonera Principal -->
        <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('secretaria.ventas.create', ['tipo' => 'cita']) }}"
                class="bg-green-500 text-white px-4 py-3 rounded-lg font-bold hover:bg-green-600 shadow flex justify-center items-center text-center">
                Agendar Cita
            </a>
            <a href="{{ route('secretaria.ventas.index') }}"
                class="bg-indigo-500 text-white px-4 py-3 rounded-lg font-bold hover:bg-indigo-600 shadow flex justify-center items-center text-center">
                Ventas de Hoy
            </a>
            <a href="{{ route('secretaria.clientes.index') }}"
                class="bg-blue-500 text-white px-4 py-3 rounded-lg font-bold hover:bg-blue-600 shadow flex justify-center items-center text-center">
                Clientes
            </a>
            <a href="{{ route('secretaria.barberos.index') }}"
                class="bg-blue-600 text-white px-4 py-3 rounded-lg font-bold hover:bg-blue-700 shadow flex justify-center items-center text-center">
                Barberos
            </a>
        </div>

        <!-- Citas de Hoy -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 border-t-4 border-blue-500">
            <h3 class="text-xl font-bold mb-4">Citas y Atenciones de Hoy ({{ now()->format('d/m/Y') }})</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-3">Hora</th>
                            <th class="p-3">Cliente</th>
                            <th class="p-3">Servicio</th>
                            <th class="p-3">Barbero Asignado</th>
                            <th class="p-3">Tipo</th>
                            <th class="p-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($citasHoy as $cita)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3 font-bold">{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse($cita->hora_fin)->format('h:i A') }}</td>
                                <td class="p-3">{{ $cita->cliente->nombre }}</td>
                                <td class="p-3">{{ $cita->servicio->nombre }}</td>
                                <td class="p-3 text-amber-700 font-semibold">{{ $cita->barbero->nombre }}</td>
                                <td class="p-3">
                                    <span
                                        class="px-2 py-1 text-xs rounded {{ $cita->tipo_atencion == 'con_cita' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $cita->tipo_atencion == 'con_cita' ? 'Cita' : 'Walk-in' }}
                                    </span>
                                </td>
                                <td class="p-3 text-center space-y-1">
                                    @if ($cita->tipo_atencion == 'con_cita')
                                        <div class="flex flex-col space-y-2">
                                            <a href="{{ route('secretaria.citas.edit', $cita->id) }}"
                                                class="bg-yellow-500 text-white px-3 py-1 rounded text-xs hover:bg-yellow-600 font-bold w-full text-center">
                                                Editar
                                            </a>
                                            <form action="{{ route('secretaria.citas.completar', $cita->id) }}"
                                                method="POST" class="w-full">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button"
                                                    class="btn-completar bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600 font-bold flex items-center justify-center w-full">
                                                    Realizado
                                                </button>
                                            </form>
                                            <form action="{{ route('secretaria.citas.cancelar', $cita->id) }}"
                                                method="POST" class="w-full">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button"
                                                    class="btn-cancelar bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 font-bold flex items-center justify-center w-full">
                                                    Cancelar
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <div class="flex flex-col space-y-2">
                                            <form action="{{ route('secretaria.citas.completar', $cita->id) }}"
                                                method="POST" class="w-full">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button"
                                                    class="btn-completar bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600 font-bold flex items-center justify-center w-full">
                                                    Finalizar
                                                </button>
                                            </form>
                                            <form action="{{ route('secretaria.citas.cancelar', $cita->id) }}"
                                                method="POST" class="w-full">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button"
                                                    class="btn-cancelar bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 font-bold flex items-center justify-center w-full">
                                                    Cancelar
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-gray-500 text-lg">No hay atenciones ni citas
                                    programadas para el día de hoy.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $citasHoy->links() }}
            </div>
        </div>

        <!-- Citas de la Semana -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 border-t-4 border-gray-400 mt-8">
            <h3 class="text-xl font-bold mb-4">Próximas Citas de esta Semana</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-3">Fecha</th>
                            <th class="p-3">Hora</th>
                            <th class="p-3">Cliente</th>
                            <th class="p-3">Servicio</th>
                            <th class="p-3">Barbero Asignado</th>
                            <th class="p-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($citasSemana as $cita)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3 font-semibold">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                                </td>
                                <td class="p-3 font-bold">{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i A') }}
                                    - {{ \Carbon\Carbon::parse($cita->hora_fin)->format('h:i A') }}</td>
                                <td class="p-3">{{ $cita->cliente->nombre }}</td>
                                <td class="p-3">{{ $cita->servicio->nombre }}</td>
                                <td class="p-3 text-amber-700 font-semibold">{{ $cita->barbero->nombre }}</td>
                                <td class="p-3 text-center space-y-1">
                                    <div class="flex flex-col space-y-2">
                                        <a href="{{ route('secretaria.citas.edit', $cita->id) }}"
                                            class="bg-yellow-500 text-white px-3 py-1 rounded text-xs hover:bg-yellow-600 font-bold w-full text-center">
                                            Editar
                                        </a>
                                        <form action="{{ route('secretaria.citas.cancelar', $cita->id) }}" method="POST"
                                            class="w-full">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button"
                                                class="btn-cancelar bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 font-bold flex items-center justify-center w-full">
                                                Cancelar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-gray-500 text-lg">No hay citas programadas
                                    para el resto de la semana.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $citasSemana->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btns = document.querySelectorAll('.btn-completar');
            btns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const form = this.closest('form');
                    Swal.fire({
                        title: '¿Confirmar Atención y Tipo de Pago?',
                        text: "Selecciona el método de pago con el que el cliente liquidó el servicio:",
                        icon: 'question',
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonColor: '#10b981', // Verde esmeralda para Contado
                        denyButtonColor: '#f59e0b', // Ambar para Crédito
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '💵 Pago en Efectivo',
                        denyButtonText: '💳 Pago con Tarjeta',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed || result.isDenied) {
                            let inputPago = form.querySelector('input[name="metodo_pago"]');
                            if (!inputPago) {
                                inputPago = document.createElement('input');
                                inputPago.type = 'hidden';
                                inputPago.name = 'metodo_pago';
                                form.appendChild(inputPago);
                            }
                            inputPago.value = result.isConfirmed ? 'efectivo' : 'tarjeta';
                            form.submit();
                        }
                    });
                });
            });

            const btnsCancelar = document.querySelectorAll('.btn-cancelar');
            btnsCancelar.forEach(btn => {
                btn.addEventListener('click', function() {
                    const form = this.closest('form');
                    Swal.fire({
                        title: '¿Cancelar Cita?',
                        text: "El espacio de esta cita volverá a quedar disponible en la agenda.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444', // Rojo tailwind para confirmar eliminación
                        cancelButtonColor: '#6b7280', // Gris para cancelar
                        confirmButtonText: 'Sí, cancelar cita',
                        cancelButtonText: 'Volver'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
