@extends('layouts.app_admin')

@section('content')
    @if(session('success'))
        <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 rounded-xl flex items-start space-x-3 text-emerald-800 text-sm font-medium animate-[fadeIn_0.3s_ease-out]">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="mb-10">
        <h2 class="text-3xl font-semibold tracking-tight mb-2">Panel General</h2>
        <p class="text-slate-500">Gestiona sucursales, personal y servicios desde un solo lugar.</p>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <!-- Action Card 1 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-300 flex flex-col group">
            <h3 class="font-semibold text-slate-900 mb-1">Sucursales</h3>
            <p class="text-sm text-slate-500 mb-6 flex-1">Añade y configura nuevas ubicaciones de barberías.</p>
            <a href="{{ route('admin.sucursales.create') }}" class="inline-flex justify-center items-center py-2 px-4 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors w-full">
                Crear Sucursal
            </a>
        </div>

        <!-- Action Card 2 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-300 flex flex-col group">
            <h3 class="font-semibold text-slate-900 mb-1">Secretarias</h3>
            <p class="text-sm text-slate-500 mb-6 flex-1">Registra secretarias y asígnalas a sucursales.</p>
            <a href="{{ route('admin.secretarias.create') }}" class="inline-flex justify-center items-center py-2 px-4 bg-white border border-slate-200 text-slate-900 text-sm font-medium rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-colors w-full">
                Crear Secretaria
            </a>
        </div>

        <!-- Action Card 3 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-300 flex flex-col group">
            <h3 class="font-semibold text-slate-900 mb-1">Servicios</h3>
            <p class="text-sm text-slate-500 mb-6 flex-1">Gestiona el catálogo de cortes y tratamientos.</p>
            <a href="{{ route('admin.servicios.index') }}" class="inline-flex justify-center items-center py-2 px-4 bg-white border border-slate-200 text-slate-900 text-sm font-medium rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-colors w-full">
                Gestionar Servicios
            </a>
        </div>

        <!-- Action Card 4: Reportes -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-300 flex flex-col group">
            <h3 class="font-semibold text-slate-900 mb-1">Reportes</h3>
            <p class="text-sm text-slate-500 mb-6 flex-1">Gestiona las ventas de sucursales y rentabilidad.</p>
            <a href="{{ route('admin.reportes.index') }}" class="inline-flex justify-center items-center py-2 px-4 bg-white border border-slate-200 text-slate-900 text-sm font-medium rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-colors w-full">
                Generar Reporte
            </a>
        </div>
    </div>

    <!-- List Section -->
    <div>
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold tracking-tight text-slate-900">Listado de Sucursales</h3>
        </div>

        @if(isset($sucursales) && $sucursales->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($sucursales as $sucursal)
                    <div class="bg-white rounded-xl p-5 border border-slate-200 flex flex-col justify-between hover:shadow-md transition-shadow group">
                        <div class="mb-4">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-semibold text-slate-900">{{ $sucursal->nombre }}</h4>
                                <span class="w-2 h-2 rounded-full bg-emerald-400 mt-2"></span>
                            </div>
                            <p class="text-sm text-slate-500 line-clamp-2">
                                {{ $sucursal->direccion ?? 'Sin dirección' }}
                            </p>
                        </div>
                        
                        <div class="flex items-center space-x-2 pt-4 border-t border-slate-100 opacity-80 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('admin.sucursales.show', $sucursal->id) }}" class="flex-1 py-1.5 px-3 text-center bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-medium rounded-md transition-colors">
                                Ver
                            </a>
                            <a href="{{ route('admin.sucursales.edit', $sucursal->id) }}" class="flex-1 py-1.5 px-3 text-center bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-medium rounded-md transition-colors">
                                Editar
                            </a>
                            <form action="{{ route('admin.sucursales.destroy', $sucursal->id) }}" method="POST" class="flex-1 form-eliminar">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full py-1.5 px-3 text-center bg-white hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 border border-slate-200 text-slate-700 text-xs font-medium rounded-md transition-colors">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">
                {{ $sucursales->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-white border border-slate-200 border-dashed rounded-2xl">
                <p class="text-slate-500 text-sm mb-4">No hay sucursales registradas en este momento.</p>
                <a href="{{ route('admin.sucursales.create') }}" class="inline-flex items-center text-sm font-medium text-slate-900 hover:text-slate-700">
                    Crear tu primera sucursal
                </a>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formsEliminar = document.querySelectorAll('.form-eliminar');
            formsEliminar.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Eliminar sucursal?',
                        text: "ATENCIÓN: Esto eliminará también a todas las secretarias y barberos que pertenezcan a ella. Esta acción no se puede deshacer.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
