@extends('layouts.app_admin')

@section('content')
<div class="max-w-2xl mx-auto">
        <div class="bg-white p-8 rounded-lg shadow-lg border-t-4 border-blue-500">
            <h2 class="text-2xl font-semibold tracking-tight text-slate-900 mb-6 text-gray-800 border-b pb-2">Datos del Cliente</h2>
            
            <form action="{{ route('secretaria.clientes.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre Completo <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" class="w-full px-3 py-3 border rounded bg-gray-50 focus:outline-none focus:border-blue-500" required>
                    @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tel√©fono</label>
                    <input type="text" name="telefono" pattern="[0-9]{10}" maxlength="10" title="Debe contener exactamente 10 dÌgitos numÈricos" value="{{ old('telefono') }}" class="w-full px-3 py-3 border rounded bg-gray-50 focus:outline-none focus:border-blue-500">
                    @error('telefono') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-8">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Correo Electr√≥nico</label>
                    <input type="email" name="correo" value="{{ old('correo') }}" class="w-full px-3 py-3 border rounded bg-gray-50 focus:outline-none focus:border-blue-500">
                    @error('correo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <button type="submit" class="w-full bg-blue-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-600 text-lg shadow-md transition">
                    Guardar Cliente en Sucursal
                </button>
            </form>
        </div>
    </div>
@endsection
