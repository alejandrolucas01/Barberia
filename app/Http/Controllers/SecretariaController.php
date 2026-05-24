<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Barbero;

class SecretariaController extends Controller
{
    // ====== CLIENTES ======
    public function indexClientes(Request $request)
    {
        $sucursal_id = auth()->user()->sucursal_id;
        
        $queryLocal = Cliente::where('sucursal_id', $sucursal_id);
        $queryOtros = Cliente::where(function($q) use ($sucursal_id) {
            $q->where('sucursal_id', '!=', $sucursal_id)
              ->orWhereNull('sucursal_id');
        });

        if ($request->has('search')) {
            $queryLocal->where(function($q) use ($request) {
                $q->where('nombre', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('telefono', 'LIKE', '%' . $request->search . '%');
            });
            $queryOtros->where(function($q) use ($request) {
                $q->where('nombre', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('telefono', 'LIKE', '%' . $request->search . '%');
            });
        }

        $clientesLocal = $queryLocal->paginate(6, ['*'], 'page_local');
        $clientesOtros = $queryOtros->with('sucursal')->paginate(6, ['*'], 'page_otros');

        return view('secretaria.clientes.index', compact('clientesLocal', 'clientesOtros'));
    }

    public function createCliente()
    {
        return view('secretaria.clientes.create');
    }

    public function storeCliente(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|digits:10',
            'correo' => 'nullable|email|max:255|unique:clientes,correo',
        ]);

        $data = $request->all();
        $data['sucursal_id'] = auth()->user()->sucursal_id;

        Cliente::create($data);
        return redirect()->route('secretaria.clientes.index')->with('success', 'Cliente registrado exitosamente.');
    }

    public function editCliente($id)
    {
        $cliente = Cliente::findOrFail($id);
        if ($cliente->sucursal_id !== auth()->user()->sucursal_id) abort(403);
        return view('secretaria.clientes.edit', compact('cliente'));
    }

    public function updateCliente(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);
        if ($cliente->sucursal_id !== auth()->user()->sucursal_id) abort(403);
        
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|digits:10',
            'correo' => 'nullable|email|max:255|unique:clientes,correo,'.$id,
        ]);

        $cliente->update($request->all());
        return redirect()->route('secretaria.clientes.index')->with('success', 'Cliente actualizado.');
    }

    public function destroyCliente($id)
    {
        $cliente = Cliente::findOrFail($id);
        if ($cliente->sucursal_id !== auth()->user()->sucursal_id) abort(403);
        $cliente->delete();
        return redirect()->route('secretaria.clientes.index')->with('success', 'Cliente eliminado.');
    }

    // ====== BARBEROS ======
    public function indexBarberos()
    {
        $sucursal_id = auth()->user()->sucursal_id;
        $barberos = Barbero::where('sucursal_id', $sucursal_id)->paginate(6);
        return view('secretaria.barberos.index', compact('barberos'));
    }

    public function createBarbero()
    {
        return view('secretaria.barberos.create');
    }

    public function storeBarbero(Request $request)
    {
        $sucursal = auth()->user()->sucursal;
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|digits:10',
            'correo' => 'nullable|email|max:255|unique:barberos,correo',
            'hora_entrada' => 'required|date_format:H:i|after_or_equal:'.$sucursal->hora_apertura,
            'hora_salida' => 'required|date_format:H:i|after:hora_entrada|before_or_equal:'.$sucursal->hora_cierre,
        ]);

        $data = $request->all();
        $data['sucursal_id'] = $sucursal->id;

        Barbero::create($data);
        return redirect()->route('secretaria.barberos.index')->with('success', 'Barbero registrado exitosamente.');
    }

    public function editBarbero($id)
    {
        $barbero = Barbero::findOrFail($id);
        if ($barbero->sucursal_id !== auth()->user()->sucursal_id) abort(403);
        return view('secretaria.barberos.edit', compact('barbero'));
    }

    public function updateBarbero(Request $request, $id)
    {
        $barbero = Barbero::findOrFail($id);
        $sucursal = auth()->user()->sucursal;
        if ($barbero->sucursal_id !== $sucursal->id) abort(403);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|digits:10',
            'correo' => 'nullable|email|max:255|unique:barberos,correo,'.$id,
            'hora_entrada' => 'required|date_format:H:i:s,H:i|after_or_equal:'.$sucursal->hora_apertura,
            'hora_salida' => 'required|date_format:H:i:s,H:i|after:hora_entrada|before_or_equal:'.$sucursal->hora_cierre,
        ]);

        $barbero->update($request->all());
        return redirect()->route('secretaria.barberos.index')->with('success', 'Barbero actualizado.');
    }

    public function destroyBarbero($id)
    {
        $barbero = Barbero::findOrFail($id);
        if ($barbero->sucursal_id !== auth()->user()->sucursal_id) abort(403);
        $barbero->delete();
        return redirect()->route('secretaria.barberos.index')->with('success', 'Barbero eliminado.');
    }

    // ====== CITAS ======
    public function editCita($id)
    {
        $cita = \App\Models\Cita::findOrFail($id);
        $sucursal_id = auth()->user()->sucursal_id;
        if ($cita->barbero->sucursal_id !== $sucursal_id) abort(403);
        
        $barberos = \App\Models\Barbero::where('sucursal_id', $sucursal_id)->get();
        return view('secretaria.citas.edit', compact('cita', 'barberos'));
    }

    public function updateCita(Request $request, $id)
    {
        $cita = \App\Models\Cita::findOrFail($id);
        if ($cita->barbero->sucursal_id !== auth()->user()->sucursal_id) abort(403);

        $request->validate([
            'barbero_id' => 'required|exists:barberos,id',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
        ]);

        $hora_inicio = \Carbon\Carbon::parse($request->hora_inicio);
        $hora_fin = $hora_inicio->copy()->addMinutes($cita->servicio->duracion_minutos);

        // Simple update without heavy conflict validation for now to allow forced changes by secretary
        $cita->update([
            'barbero_id' => $request->barbero_id,
            'fecha' => $request->fecha,
            'hora_inicio' => $hora_inicio->format('H:i'),
            'hora_fin' => $hora_fin->format('H:i')
        ]);

        return redirect()->route('secretaria.dashboard')->with('success', 'Cita actualizada exitosamente.');
    }

    public function completarCita(Request $request, $id)
    {
        $cita = \App\Models\Cita::findOrFail($id);
        
        // Verifica que la cita pertenezca a la sucursal de la secretaria
        $sucursal_id = auth()->user()->sucursal_id;
        $barbero_sucursal_id = $cita->barbero->sucursal_id;
        if ($barbero_sucursal_id !== $sucursal_id) {
            abort(403);
        }

        $metodo_pago = $request->input('metodo_pago', 'efectivo');
        $cita->update([
            'estado' => 'completada',
            'metodo_pago' => $metodo_pago
        ]);

        return redirect()->route('secretaria.dashboard')->with('success', 'Cita marcada como realizada exitosamente.');
    }

    public function cancelarCita($id)
    {
        $cita = \App\Models\Cita::findOrFail($id);
        
        $sucursal_id = auth()->user()->sucursal_id;
        if ($cita->barbero->sucursal_id !== $sucursal_id) {
            abort(403);
        }

        $cita->update(['estado' => 'cancelada']);

        return redirect()->route('secretaria.dashboard')->with('success', 'Cita cancelada exitosamente.');
    }
}

