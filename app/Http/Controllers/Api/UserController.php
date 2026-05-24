<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }

        $users = $query->get();
        return response()->json($users, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:administrador,secretaria',
            'sucursal_id' => 'nullable|required_if:role,secretaria|exists:sucursales,id',
        ]);

        // Max 2 secretarias per branch constraint (matching web behavior)
        if ($validated['role'] === 'secretaria' && isset($validated['sucursal_id'])) {
            $existingCount = User::where('role', 'secretaria')
                ->where('sucursal_id', $validated['sucursal_id'])
                ->count();
            if ($existingCount >= 2) {
                return response()->json(['error' => 'This branch already has the maximum of 2 secretarias assigned.'], 400);
            }
        }

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = User::with('sucursal')->find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json($user, 200);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'sometimes|required|in:administrador,secretaria',
            'sucursal_id' => 'nullable|required_if:role,secretaria|exists:sucursales,id',
        ]);

        if (isset($validated['role']) && $validated['role'] === 'secretaria' && isset($validated['sucursal_id'])) {
            if ($validated['sucursal_id'] != $user->sucursal_id) {
                $existingCount = User::where('role', 'secretaria')
                    ->where('sucursal_id', $validated['sucursal_id'])
                    ->count();
                if ($existingCount >= 2) {
                    return response()->json(['error' => 'This branch already has the maximum of 2 secretarias assigned.'], 400);
                }
            }
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        return response()->json($user, 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
