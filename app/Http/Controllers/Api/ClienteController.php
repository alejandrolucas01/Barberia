<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();
        
        if ($request->has('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', '%' . $search . '%')
                  ->orWhere('telefono', 'LIKE', '%' . $search . '%')
                  ->orWhere('correo', 'LIKE', '%' . $search . '%');
            });
        }

        $clientes = $query->get();
        return response()->json($clientes, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|digits:10',
            'correo' => 'nullable|email|max:255|unique:clientes,correo',
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ]);

        $cliente = Cliente::create($validated);
        return response()->json($cliente, 201);
    }

    public function show($id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['error' => 'Cliente not found'], 404);
        }
        return response()->json($cliente, 200);
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['error' => 'Cliente not found'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'telefono' => 'nullable|digits:10',
            'correo' => 'nullable|email|max:255|unique:clientes,correo,' . $id,
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ]);

        $cliente->update($validated);
        return response()->json($cliente, 200);
    }

    public function destroy($id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['error' => 'Cliente not found'], 404);
        }
        $cliente->delete();
        return response()->json(['message' => 'Cliente deleted successfully'], 200);
    }
}
