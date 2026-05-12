<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Barbero;
use App\Models\Cita;
use Carbon\Carbon;

class VentaController extends Controller
{
    public function index()
    {
        $sucursal_id = auth()->user()->sucursal_id;
        $barberosIds = Barbero::where('sucursal_id', $sucursal_id)->pluck('id');
        
        $baseQuery = Cita::whereIn('barbero_id', $barberosIds)
            ->where('fecha', date('Y-m-d'))
            ->where('estado', '!=', 'pendiente');
            
        $totalVentas = (clone $baseQuery)->where('estado', 'completada')->with('servicio')->get()->sum(function($cita) {
            return $cita->servicio->precio;
        });
        
        $ventas = $baseQuery->with(['barbero', 'cliente', 'servicio'])
            ->orderBy('hora_inicio', 'desc')
            ->paginate(6);

        return view('secretaria.ventas.index', compact('ventas', 'totalVentas'));
    }

    public function create(Request $request)
    {
        $tipo = $request->query('tipo', 'venta');
        $sucursal_id = auth()->user()->sucursal_id;
        $clientes = Cliente::all();
        $servicios = Servicio::all();
        $barberos = Barbero::where('sucursal_id', $sucursal_id)->get();

        return view('secretaria.ventas.create', compact('clientes', 'servicios', 'barberos', 'tipo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'servicios' => 'required|array',
            'servicios.*' => 'exists:servicios,id',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'tipo_atencion' => 'required|in:con_cita,sin_cita',
        ]);
        
        $sucursal = auth()->user()->sucursal;
        $sucursal_id = $sucursal->id;
        $barberos = Barbero::where('sucursal_id', $sucursal_id)->get();
        
        if ($barberos->isEmpty()) {
            return redirect()->back()->with('error', 'No hay barberos registrados en esta sucursal para asignar la venta.');
        }

        $fechaHoraInicio = Carbon::parse($request->fecha . ' ' . $request->hora_inicio);
        
        if ($fechaHoraInicio->lessThan(now()->startOfMinute())) {
            return redirect()->back()->with('error', 'No se puede agendar: la fecha y hora seleccionadas ya pasaron.');
        }

        $hora_inicio = Carbon::parse($request->hora_inicio);
        
        $serviciosSeleccionados = Servicio::whereIn('id', $request->servicios)->get();
        $totalMinutes = $serviciosSeleccionados->sum('duracion_minutos');
        
        $hora_fin = $hora_inicio->copy()->addMinutes($totalMinutes);
        
        $hora_cierre_sucursal = Carbon::parse($sucursal->hora_cierre);
        if ($hora_fin->greaterThanOrEqualTo($hora_cierre_sucursal->copy()->addMinutes(30))) {
            return redirect()->back()->with('error', 'No se puede agendar: el tiempo de los servicios es 30 min o más después de la hora de cierre.');
        }

        // Auto-assign available barber logic
        $barbero_id = null;
        foreach ($barberos as $barbero) {
            $hora_entrada = Carbon::parse($barbero->hora_entrada);
            $hora_salida = Carbon::parse($barbero->hora_salida);
            
            // Check if within barber's working hours (allowing up to 29 min overlap at the end)
            if ($hora_inicio->lessThan($hora_entrada) || $hora_fin->greaterThanOrEqualTo($hora_salida->copy()->addMinutes(30))) {
                continue;
            }

            $conflict = Cita::where('barbero_id', $barbero->id)
                ->where('fecha', $request->fecha)
                ->where('estado', 'pendiente')
                ->where(function($query) use ($hora_inicio, $hora_fin) {
                    $query->whereBetween('hora_inicio', [$hora_inicio->format('H:i:s'), $hora_fin->format('H:i:s')])
                          ->orWhereBetween('hora_fin', [$hora_inicio->format('H:i:s'), $hora_fin->format('H:i:s')])
                          ->orWhere(function($q) use ($hora_inicio, $hora_fin) {
                              $q->where('hora_inicio', '<=', $hora_inicio->format('H:i:s'))
                                ->where('hora_fin', '>=', $hora_fin->format('H:i:s'));
                          });
                })->exists();
            
            if (!$conflict) {
                $barbero_id = $barbero->id;
                break;
            }
        }

        if (!$barbero_id) {
            return redirect()->back()->with('error', 'No hay barberos disponibles en el horario solicitado (choca con otra cita).');
        }

        $currentTime = $hora_inicio->copy();
        foreach ($serviciosSeleccionados as $servicio) {
            $endTime = $currentTime->copy()->addMinutes($servicio->duracion_minutos);
            
            Cita::create([
                'barbero_id' => $barbero_id,
                'cliente_id' => $request->cliente_id,
                'servicio_id' => $servicio->id,
                'fecha' => $request->fecha,
                'hora_inicio' => $currentTime->format('H:i'),
                'hora_fin' => $endTime->format('H:i'),
                'tipo_atencion' => $request->tipo_atencion,
                'estado' => 'pendiente'
            ]);
            
            $currentTime = $endTime;
        }

        $barberoObj = Barbero::find($barbero_id);
        $texto = $request->tipo_atencion == 'con_cita' ? 'Cita programada' : 'Atención registrada';

        return redirect()->route('secretaria.dashboard')->with('success', $texto . ' exitosamente. Asignado al barbero: ' . $barberoObj->nombre);
    }
}
