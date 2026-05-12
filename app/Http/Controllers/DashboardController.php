<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'administrador') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'secretaria') {
            return redirect()->route('secretaria.dashboard');
        }

        return view('dashboard');
    }

    public function admin()
    {
        $sucursales = \App\Models\sucursales::paginate(6);
        return view('dashboard_admin', compact('sucursales'));
    }

    public function secretaria(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'secretaria') {
            abort(403);
        }

        $sucursal_id = $user->sucursal_id;
        
        $barberosIds = \App\Models\Barbero::where('sucursal_id', $sucursal_id)->pluck('id');
        
        $citasHoy = \App\Models\Cita::with(['barbero', 'cliente', 'servicio'])
            ->whereIn('barbero_id', $barberosIds)
            ->where('fecha', now()->format('Y-m-d'))
            ->where('estado', 'pendiente')
            ->orderBy('hora_inicio', 'asc')
            ->paginate(6, ['*'], 'hoy');

        $inicioSemana = now()->addDay()->format('Y-m-d');
        $finSemana = now()->endOfWeek()->format('Y-m-d');

        $citasSemana = \App\Models\Cita::with(['barbero', 'cliente', 'servicio'])
            ->whereIn('barbero_id', $barberosIds)
            ->whereBetween('fecha', [$inicioSemana, $finSemana])
            ->where('estado', 'pendiente')
            ->orderBy('fecha', 'asc')
            ->orderBy('hora_inicio', 'asc')
            ->paginate(6, ['*'], 'semana');

        return view('dashboard_secretaria', compact('citasHoy', 'citasSemana'));
    }
}
