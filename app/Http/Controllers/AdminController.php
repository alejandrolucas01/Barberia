<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\sucursales;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function createSucursal()
    {
        return view('admin.sucursales.create');
    }

    public function storeSucursal(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|digits:10',
            'dias_apertura' => 'nullable|array',
            'hora_apertura' => 'nullable|date_format:H:i',
            'hora_cierre' => 'nullable|date_format:H:i',
        ]);

        sucursales::create($request->all());

        return redirect()->route('admin.dashboard')->with('success', 'Sucursal creada exitosamente.');
    }

    public function editSucursal($id)
    {
        $sucursal = sucursales::findOrFail($id);
        return view('admin.sucursales.edit', compact('sucursal'));
    }

    public function updateSucursal(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|digits:10',
            'dias_apertura' => 'nullable|array',
            'hora_apertura' => 'nullable|date_format:H:i',
            'hora_cierre' => 'nullable|date_format:H:i',
        ]);

        $sucursal = sucursales::findOrFail($id);
        $sucursal->update($request->all());

        return redirect()->route('admin.dashboard')->with('success', 'Sucursal actualizada exitosamente.');
    }

    public function showSucursal($id)
    {
        $sucursal = sucursales::with(['secretarias', 'barberos'])->findOrFail($id);
        return view('admin.sucursales.show', compact('sucursal'));
    }

    public function destroySucursal($id)
    {
        $sucursal = sucursales::findOrFail($id);
        
        // Eliminar las secretarias asignadas a esta sucursal (los barberos se eliminan en cascada por la base de datos)
        $sucursal->secretarias()->delete(); 
        
        $sucursal->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Sucursal eliminada exitosamente.');
    }

    public function createSecretaria()
    {
        $sucursales = sucursales::all();
        return view('admin.secretarias.create', compact('sucursales'));
    }

    public function storeSecretaria(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'sucursal_id' => 'required|exists:sucursales,id',
        ]);

        $sucursal = sucursales::findOrFail($request->sucursal_id);
        if ($sucursal->secretarias()->count() >= 2) {
            return back()->withErrors(['sucursal_id' => 'Esta sucursal ya tiene el máximo de 2 secretarias asignadas.'])->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'secretaria',
            'sucursal_id' => $request->sucursal_id,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Secretaria creada exitosamente y asignada a la sucursal.');
    }

    public function editSecretaria($id)
    {
        $secretaria = User::where('role', 'secretaria')->findOrFail($id);
        $sucursales = sucursales::all();
        return view('admin.secretarias.edit', compact('secretaria', 'sucursales'));
    }

    public function updateSecretaria(Request $request, $id)
    {
        $secretaria = User::where('role', 'secretaria')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'password' => 'nullable|string|min:6|confirmed',
            'sucursal_id' => 'required|exists:sucursales,id',
        ]);

        if ($request->sucursal_id != $secretaria->sucursal_id) {
            $sucursal = sucursales::findOrFail($request->sucursal_id);
            if ($sucursal->secretarias()->count() >= 2) {
                return back()->withErrors(['sucursal_id' => 'Esta sucursal ya tiene el máximo de 2 secretarias asignadas.'])->withInput();
            }
        }

        $secretaria->name = $request->name;
        $secretaria->email = $request->email;
        $secretaria->sucursal_id = $request->sucursal_id;

        if ($request->filled('password')) {
            $secretaria->password = Hash::make($request->password);
        }

        $secretaria->save();

        return redirect()->route('admin.sucursales.show', $secretaria->sucursal_id)->with('success', 'Secretaria actualizada exitosamente.');
    }

    public function destroySecretaria($id)
    {
        $secretaria = User::where('role', 'secretaria')->findOrFail($id);
        $sucursal_id = $secretaria->sucursal_id;
        $secretaria->delete();

        return redirect()->route('admin.sucursales.show', $sucursal_id)->with('success', 'Secretaria eliminada exitosamente.');
    }

    public function destroyBarbero($id)
    {
        $barbero = \App\Models\Barbero::findOrFail($id);
        $sucursal_id = $barbero->sucursal_id;
        $barbero->delete();

        return redirect()->route('admin.sucursales.show', $sucursal_id)->with('success', 'Barbero eliminado exitosamente.');
    }
}

