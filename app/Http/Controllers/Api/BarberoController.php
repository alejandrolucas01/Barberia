<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barbero;
use App\Models\sucursales;
use Illuminate\Http\Request;

class BarberoController extends Controller
{
    public function index(Request $request)
    {
        $query = Barbero::query();
        if ($request->has('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }
        $barberos = $query->get();
        return response()->json($barberos, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|digits:10',
            'correo' => 'nullable|email|max:255|unique:barberos,correo',
            'hora_entrada' => 'required|date_format:H:i',
            'hora_salida' => 'required|date_format:H:i|after:hora_entrada',
            'sucursal_id' => 'required|exists:sucursales,id',
        ]);

        $barbero = Barbero::create($validated);
        return response()->json($barbero, 201);
    }

    public function show($id)
    {
        $barbero = Barbero::find($id);
        if (!$barbero) {
            return response()->json(['error' => 'Barbero not found'], 404);
        }
        return response()->json($barbero, 200);
    }

    public function update(Request $request, $id)
    {
        $barbero = Barbero::find($id);
        if (!$barbero) {
            return response()->json(['error' => 'Barbero not found'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'telefono' => 'nullable|digits:10',
            'correo' => 'nullable|email|max:255|unique:barberos,correo,' . $id,
            'hora_entrada' => 'sometimes|required|date_format:H:i',
            'hora_salida' => 'sometimes|required|date_format:H:i|after:hora_entrada',
            'sucursal_id' => 'sometimes|required|exists:sucursales,id',
        ]);

        $barbero->update($validated);
        return response()->json($barbero, 200);
    }

    public function destroy($id)
    {
        $barbero = Barbero::find($id);
        if (!$barbero) {
            return response()->json(['error' => 'Barbero not found'], 404);
        }
        $barbero->delete();
        return response()->json(['message' => 'Barbero deleted successfully'], 200);
    }
}
