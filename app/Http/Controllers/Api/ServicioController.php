<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function index()
    {
        $servicios = Servicio::all();
        return response()->json($servicios, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'duracion_minutos' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
        ]);

        $servicio = Servicio::create($validated);
        return response()->json($servicio, 201);
    }

    public function show($id)
    {
        $servicio = Servicio::find($id);
        if (!$servicio) {
            return response()->json(['error' => 'Servicio not found'], 404);
        }
        return response()->json($servicio, 200);
    }

    public function update(Request $request, $id)
    {
        $servicio = Servicio::find($id);
        if (!$servicio) {
            return response()->json(['error' => 'Servicio not found'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'duracion_minutos' => 'sometimes|required|integer|min:1',
            'precio' => 'sometimes|required|numeric|min:0',
        ]);

        $servicio->update($validated);
        return response()->json($servicio, 200);
    }

    public function destroy($id)
    {
        $servicio = Servicio::find($id);
        if (!$servicio) {
            return response()->json(['error' => 'Servicio not found'], 404);
        }
        $servicio->delete();
        return response()->json(['message' => 'Servicio deleted successfully'], 200);
    }
}
