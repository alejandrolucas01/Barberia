<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\sucursales;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index()
    {
        $sucursales = sucursales::all();
        return response()->json($sucursales, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|digits:10',
            'dias_apertura' => 'nullable|array',
            'hora_apertura' => 'nullable|date_format:H:i',
            'hora_cierre' => 'nullable|date_format:H:i',
        ]);

        $sucursal = sucursales::create($validated);
        return response()->json($sucursal, 201);
    }

    public function show($id)
    {
        $sucursal = sucursales::with(['secretarias', 'barberos'])->find($id);
        if (!$sucursal) {
            return response()->json(['error' => 'Sucursal not found'], 404);
        }
        return response()->json($sucursal, 200);
    }

    public function update(Request $request, $id)
    {
        $sucursal = sucursales::find($id);
        if (!$sucursal) {
            return response()->json(['error' => 'Sucursal not found'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|digits:10',
            'dias_apertura' => 'nullable|array',
            'hora_apertura' => 'nullable|date_format:H:i',
            'hora_cierre' => 'nullable|date_format:H:i',
        ]);

        $sucursal->update($validated);
        return response()->json($sucursal, 200);
    }

    public function destroy($id)
    {
        $sucursal = sucursales::find($id);
        if (!$sucursal) {
            return response()->json(['error' => 'Sucursal not found'], 404);
        }

        // Delete associated secretarias (matching web behavior)
        $sucursal->secretarias()->delete();
        $sucursal->delete();

        return response()->json(['message' => 'Sucursal deleted successfully'], 200);
    }
}
