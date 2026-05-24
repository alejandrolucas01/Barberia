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

    public function reportesIndex(\Illuminate\Http\Request $request)
    {
        $todasSucursales = \App\Models\sucursales::all();

        // Calcular ventas por sucursal
        $ventasPorSucursal = [];
        $totalVentasGeneral = 0;
        $totalEfectivoGeneral = 0;
        $totalTarjetaGeneral = 0;
        $chartLabels = [];
        $chartData = [];

        // Filtro opcional por sucursal
        $sucursalFilterId = $request->get('sucursal_id');

        $totalCanceladasGeneral = 0;

        foreach ($todasSucursales as $suc) {
            $barberoIds = \App\Models\Barbero::where('sucursal_id', $suc->id)->pluck('id');
            
            $query = \App\Models\Cita::with('servicio')
                ->whereIn('barbero_id', $barberoIds)
                ->where('estado', 'completada');

            $citasCompletadas = $query->get();
            
            $citasCanceladasCount = \App\Models\Cita::whereIn('barbero_id', $barberoIds)
                ->where('estado', 'cancelada')
                ->count();
            
            $suma = $citasCompletadas->sum(function($c) {
                return $c->servicio?->precio ?? 0;
            });

            $sumaTarjeta = $citasCompletadas->where('metodo_pago', 'tarjeta')->sum(function($c) {
                return $c->servicio?->precio ?? 0;
            });
            $sumaEfectivo = $suma - $sumaTarjeta;

            $ventasPorSucursal[$suc->id] = [
                'nombre' => $suc->nombre,
                'total' => $suma,
                'efectivo' => $sumaEfectivo,
                'tarjeta' => $sumaTarjeta,
                'count' => $citasCompletadas->count(),
                'canceladas' => $citasCanceladasCount,
            ];

            $totalVentasGeneral += $suma;
            $totalEfectivoGeneral += $sumaEfectivo;
            $totalTarjetaGeneral += $sumaTarjeta;
            $totalCanceladasGeneral += $citasCanceladasCount;
            $chartLabels[] = $suc->nombre;
            $chartData[] = $suma;
        }

        return view('admin.reportes.index', compact(
            'todasSucursales', 
            'ventasPorSucursal', 
            'totalVentasGeneral',
            'totalEfectivoGeneral',
            'totalTarjetaGeneral',
            'totalCanceladasGeneral',
            'chartLabels',
            'chartData',
            'sucursalFilterId'
        ));
    }

    public function reporteGeneral()
    {
        $todasSucursales = \App\Models\sucursales::all();
        $ventasPorSucursal = [];
        $totalVentasGeneral = 0;
        $totalEfectivoGeneral = 0;
        $totalTarjetaGeneral = 0;
        $totalCanceladasGeneral = 0;

        foreach ($todasSucursales as $suc) {
            $barberoIds = \App\Models\Barbero::where('sucursal_id', $suc->id)->pluck('id');
            
            $citasCompletadas = \App\Models\Cita::with('servicio')
                ->whereIn('barbero_id', $barberoIds)
                ->where('estado', 'completada')
                ->get();
                
            $citasCanceladasCount = \App\Models\Cita::whereIn('barbero_id', $barberoIds)
                ->where('estado', 'cancelada')
                ->count();
            
            $suma = $citasCompletadas->sum(function($c) {
                return $c->servicio?->precio ?? 0;
            });

            $sumaTarjeta = $citasCompletadas->where('metodo_pago', 'tarjeta')->sum(function($c) {
                return $c->servicio?->precio ?? 0;
            });
            $sumaEfectivo = $suma - $sumaTarjeta;

            $ventasPorSucursal[$suc->id] = [
                'nombre' => $suc->nombre,
                'total' => $suma,
                'efectivo' => $sumaEfectivo,
                'tarjeta' => $sumaTarjeta,
                'count' => $citasCompletadas->count(),
                'canceladas' => $citasCanceladasCount,
            ];

            $totalVentasGeneral += $suma;
            $totalEfectivoGeneral += $sumaEfectivo;
            $totalTarjetaGeneral += $sumaTarjeta;
            $totalCanceladasGeneral += $citasCanceladasCount;
        }

        $pdf = app('dompdf.wrapper')->loadView('admin.reportes.general_pdf', compact('todasSucursales', 'ventasPorSucursal', 'totalVentasGeneral', 'totalEfectivoGeneral', 'totalTarjetaGeneral', 'totalCanceladasGeneral'));
        return $pdf->download('reporte_ventas_general_' . date('Y_m_d') . '.pdf');
    }

    public function reporteSucursal(\Illuminate\Http\Request $request)
    {
        $sucursal_id = $request->get('sucursal_id');
        $sucursal = \App\Models\sucursales::findOrFail($sucursal_id);
        
        $barberoIds = \App\Models\Barbero::where('sucursal_id', $sucursal_id)->pluck('id');
        $ventas = \App\Models\Cita::with(['barbero', 'cliente', 'servicio'])
            ->whereIn('barbero_id', $barberoIds)
            ->where('estado', '!=', 'pendiente')
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->get();

        $completadas = $ventas->where('estado', 'completada');
        $totalVentas = $completadas->sum(function($c) {
            return $c->servicio?->precio ?? 0;
        });
        $totalTarjeta = $completadas->where('metodo_pago', 'tarjeta')->sum(function($c) {
            return $c->servicio?->precio ?? 0;
        });
        $totalEfectivo = $totalVentas - $totalTarjeta;

        $pdf = app('dompdf.wrapper')->loadView('admin.reportes.sucursal_pdf', compact('sucursal', 'ventas', 'totalVentas', 'totalEfectivo', 'totalTarjeta'));
        $safeName = str_replace([' ', '/'], '_', $sucursal->nombre);
        return $pdf->download('reporte_sucursal_' . $safeName . '_' . date('Y_m_d') . '.pdf');
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
