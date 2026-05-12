@extends('layouts.app_admin')

@section('content')
<div class="">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 mb-8">
            <h2 class="text-2xl font-bold mb-2">Bienvenido, {{ auth()->user()->name ?? 'Cliente' }}</h2>
            <p class="text-gray-600">Aquí podrás ver y agendar tus próximas citas.</p>
        </div>
    </div>
@endsection
