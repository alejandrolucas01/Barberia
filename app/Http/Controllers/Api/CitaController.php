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
        $query = Cita::with(['barbero', 'cliente']);

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
        // Support both 'servicios' (array) or 'servicio_id' (single ID)
        if ($request->has('servicio_id') && !$request->has('servicios')) {
            $request->merge(['servicios' => [$request->servicio_id]]);
        }

        $validated = $request->validate([
            'barbero_id' => 'required|exists:barberos,id',
            'cliente_id' => 'required|exists:clientes,id',
            'servicios' => 'required|array',
            'servicios.*' => 'exists:servicios,id',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'tipo_atencion' => 'required|in:con_cita,sin_cita',
            'estado' => 'nullable|in:pendiente,completada,cancelada',
            'metodo_pago' => 'nullable|in:efectivo,tarjeta',
        ]);

        $serviciosSeleccionados = Servicio::whereIn('id', $validated['servicios'])->get();
        $totalMinutes = $serviciosSeleccionados->sum('duracion_minutos');

        $hora_inicio = Carbon::parse($validated['hora_inicio']);
        $hora_fin = $hora_inicio->copy()->addMinutes($totalMinutes);

        $validated['hora_fin'] = $hora_fin->format('H:i');
        $validated['estado'] = $validated['estado'] ?? 'pendiente';
        $validated['servicio_id'] = $serviciosSeleccionados->first()->id;

        $conflict = Cita::where('barbero_id', $validated['barbero_id'])
            ->where('fecha', $validated['fecha'])
            ->where('estado', 'pendiente')
            ->where('cliente_id', '!=', $validated['cliente_id'])
            ->where('hora_inicio', '<', $hora_fin->format('H:i'))
            ->where('hora_fin', '>', $hora_inicio->format('H:i'))
            ->exists();

        if ($conflict) {
            return response()->json(['error' => 'Conflict with another appointment.'], 409);
        }

        $citaData = $validated;
        unset($citaData['servicios']);

        $cita = Cita::create($citaData);
        $cita->servicios()->attach($validated['servicios']);

        return response()->json($cita->load(['barbero', 'cliente']), 201);
    }

    public function show($id)
    {
        $cita = Cita::with(['barbero', 'cliente'])->find($id);
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

        if ($request->has('servicio_id') && !$request->has('servicios')) {
            $request->merge(['servicios' => [$request->servicio_id]]);
        }

        $validated = $request->validate([
            'barbero_id' => 'sometimes|required|exists:barberos,id',
            'cliente_id' => 'sometimes|required|exists:clientes,id',
            'servicios' => 'sometimes|required|array',
            'servicios.*' => 'exists:servicios,id',
            'fecha' => 'sometimes|required|date',
            'hora_inicio' => 'sometimes|required|date_format:H:i',
            'tipo_atencion' => 'sometimes|required|in:con_cita,sin_cita',
            'estado' => 'sometimes|required|in:pendiente,completada,cancelada',
            'metodo_pago' => 'nullable|in:efectivo,tarjeta',
        ]);

        $barberoId = $validated['barbero_id'] ?? $cita->barbero_id;
        $fecha = $validated['fecha'] ?? $cita->fecha;
        $clienteId = $validated['cliente_id'] ?? $cita->cliente_id;
        $horaInicioStr = $validated['hora_inicio'] ?? $cita->hora_inicio;
        $serviciosIds = $validated['servicios'] ?? $cita->servicios->pluck('id')->toArray();

        $serviciosSeleccionados = Servicio::whereIn('id', $serviciosIds)->get();
        $totalMinutes = $serviciosSeleccionados->sum('duracion_minutos');
        $hora_inicio = Carbon::parse($horaInicioStr);
        $hora_fin = $hora_inicio->copy()->addMinutes($totalMinutes);

        // Check for conflicts excluding this appointment
        $conflict = Cita::where('id', '!=', $id)
            ->where('barbero_id', $barberoId)
            ->where('fecha', $fecha)
            ->where('estado', 'pendiente')
            ->where('cliente_id', '!=', $clienteId)
            ->where('hora_inicio', '<', $hora_fin->format('H:i'))
            ->where('hora_fin', '>', $hora_inicio->format('H:i'))
            ->exists();

        if ($conflict) {
            return response()->json(['error' => 'Conflict with another appointment.'], 409);
        }

        if (isset($validated['servicios']) || isset($validated['hora_inicio'])) {
            $validated['hora_fin'] = $hora_fin->format('H:i');
            $validated['servicio_id'] = $serviciosSeleccionados->first()->id;
        }

        $citaData = $validated;
        unset($citaData['servicios']);

        $cita->update($citaData);

        if (isset($validated['servicios'])) {
            $cita->servicios()->sync($validated['servicios']);
        }

        return response()->json($cita->load(['barbero', 'cliente']), 200);
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

        return response()->json($cita->load(['barbero', 'cliente']), 200);
    }

    public function cancelar($id)
    {
        $cita = Cita::find($id);
        if (!$cita) {
            return response()->json(['error' => 'Cita not found'], 404);
        }

        $cita->update(['estado' => 'cancelada']);

        return response()->json($cita->load(['barbero', 'cliente']), 200);
    }
}
