<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Barbero;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $query = Cita::with(['barbero', 'cliente', 'servicio']);

        if ($request->has('fecha')) {
            $query->where('fecha', $request->fecha);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('barbero_id')) {
            $query->where('barbero_id', $request->barbero_id);
        }

        if ($request->has('sucursal_id')) {
            $sucursal_id = $request->sucursal_id;
            $query->whereHas('barbero', function($q) use ($sucursal_id) {
                $q->where('sucursal_id', $sucursal_id);
            });
        }

        $citas = $query->orderBy('fecha', 'desc')
                      ->orderBy('hora_inicio', 'desc')
                      ->get();

        return response()->json($citas, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barbero_id' => 'required|exists:barberos,id',
            'cliente_id' => 'required|exists:clientes,id',
            'servicio_id' => 'required|exists:servicios,id',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'tipo_atencion' => 'required|in:con_cita,sin_cita',
            'estado' => 'nullable|in:pendiente,completada,cancelada',
            'metodo_pago' => 'nullable|in:efectivo,tarjeta',
        ]);

        // Calculate hora_fin based on service duration
        $servicio = Servicio::findOrFail($validated['servicio_id']);
        $hora_inicio = Carbon::parse($validated['hora_inicio']);
        $hora_fin = $hora_inicio->copy()->addMinutes($servicio->duracion_minutos);

        $validated['hora_fin'] = $hora_fin->format('H:i');
        $validated['estado'] = $validated['estado'] ?? 'pendiente';

        // Check for conflicts
        $conflict = Cita::where('barbero_id', $validated['barbero_id'])
            ->where('fecha', $validated['fecha'])
            ->where('estado', 'pendiente')
            ->where(function($query) use ($hora_inicio, $hora_fin) {
                $query->whereBetween('hora_inicio', [$hora_inicio->format('H:i'), $hora_fin->format('H:i')])
                      ->orWhereBetween('hora_fin', [$hora_inicio->format('H:i'), $hora_fin->format('H:i')])
                      ->orWhere(function($q) use ($hora_inicio, $hora_fin) {
                          $q->where('hora_inicio', '<=', $hora_inicio->format('H:i'))
                            ->where('hora_fin', '>=', $hora_fin->format('H:i'));
                      });
            })->exists();

        if ($conflict) {
            return response()->json(['error' => 'Conflict with another appointment.'], 409);
        }

        $cita = Cita::create($validated);
        return response()->json($cita->load(['barbero', 'cliente', 'servicio']), 201);
    }

    public function show($id)
    {
        $cita = Cita::with(['barbero', 'cliente', 'servicio'])->find($id);
        if (!$cita) {
            return response()->json(['error' => 'Cita not found'], 404);
        }
        return response()->json($cita, 200);
    }

    public function update(Request $request, $id)
    {
        $cita = Cita::find($id);
        if (!$cita) {
            return response()->json(['error' => 'Cita not found'], 404);
        }

        $validated = $request->validate([
            'barbero_id' => 'sometimes|required|exists:barberos,id',
            'cliente_id' => 'sometimes|required|exists:clientes,id',
            'servicio_id' => 'sometimes|required|exists:servicios,id',
            'fecha' => 'sometimes|required|date',
            'hora_inicio' => 'sometimes|required|date_format:H:i',
            'tipo_atencion' => 'sometimes|required|in:con_cita,sin_cita',
            'estado' => 'sometimes|required|in:pendiente,completada,cancelada',
            'metodo_pago' => 'nullable|in:efectivo,tarjeta',
        ]);

        if (isset($validated['hora_inicio']) || isset($validated['servicio_id'])) {
            $servicioId = $validated['servicio_id'] ?? $cita->servicio_id;
            $servicio = Servicio::findOrFail($servicioId);
            $horaInicioStr = $validated['hora_inicio'] ?? $cita->hora_inicio;
            $hora_inicio = Carbon::parse($horaInicioStr);
            $hora_fin = $hora_inicio->copy()->addMinutes($servicio->duracion_minutos);
            $validated['hora_fin'] = $hora_fin->format('H:i');
        }

        $cita->update($validated);
        return response()->json($cita->load(['barbero', 'cliente', 'servicio']), 200);
    }

    public function destroy($id)
    {
        $cita = Cita::find($id);
        if (!$cita) {
            return response()->json(['error' => 'Cita not found'], 404);
        }
        $cita->delete();
        return response()->json(['message' => 'Cita deleted successfully'], 200);
    }

    public function completar(Request $request, $id)
    {
        $cita = Cita::find($id);
        if (!$cita) {
            return response()->json(['error' => 'Cita not found'], 404);
        }

        $validated = $request->validate([
            'metodo_pago' => 'required|in:efectivo,tarjeta',
        ]);

        $cita->update([
            'estado' => 'completada',
            'metodo_pago' => $validated['metodo_pago']
        ]);

        return response()->json($cita->load(['barbero', 'cliente', 'servicio']), 200);
    }

    public function cancelar($id)
    {
        $cita = Cita::find($id);
        if (!$cita) {
            return response()->json(['error' => 'Cita not found'], 404);
        }

        $cita->update(['estado' => 'cancelada']);

        return response()->json($cita->load(['barbero', 'cliente', 'servicio']), 200);
    }
}
