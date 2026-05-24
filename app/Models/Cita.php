<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $fillable = [
        'barbero_id',
        'cliente_id',
        'servicio_id', // Kept for DB column constraint backward compatibility
        'fecha',
        'hora_inicio',
        'hora_fin',
        'tipo_atencion',
        'estado',
        'metodo_pago'
    ];

    /**
     * Eager load the many-to-many services relationship automatically to avoid N+1 queries.
     */
    protected $with = ['servicios'];

    protected static function booted()
    {
        static::created(function ($cita) {
            $cita->loadMissing(['cliente', 'barbero', 'servicios']);
            if ($cita->tipo_atencion === 'con_cita' && $cita->cliente?->correo) {
                try {
                    \Illuminate\Support\Facades\Mail::to($cita->cliente->correo)
                        ->send(new \App\Mail\CitaAgendada($cita));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Error al enviar correo de cita agendada: ' . $e->getMessage());
                }
            }
        });

        static::updated(function ($cita) {
            $cita->loadMissing(['cliente', 'barbero', 'servicios']);
            if ($cita->isDirty('estado') && $cita->cliente?->correo) {
                try {
                    if ($cita->estado === 'cancelada') {
                        \Illuminate\Support\Facades\Mail::to($cita->cliente->correo)
                            ->send(new \App\Mail\CitaCancelada($cita));
                    } elseif ($cita->estado === 'completada') {
                        \Illuminate\Support\Facades\Mail::to($cita->cliente->correo)
                            ->send(new \App\Mail\CitaCompletada($cita));
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Error al enviar correo de cambio de estado de cita: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Get the virtual consolidated service (combining all selected services).
     */
    public function getServicioAttribute()
    {
        $servicios = $this->servicios;
        if ($servicios->isEmpty()) {
            return $this->getRelationValue('servicio');
        }

        $virtualServicio = new \stdClass();
        $virtualServicio->id = $servicios->first()->id;
        $virtualServicio->nombre = $servicios->pluck('nombre')->join(', ');
        $virtualServicio->precio = $servicios->sum('precio');
        $virtualServicio->duracion_minutos = $servicios->sum('duracion_minutos');

        return $virtualServicio;
    }

    public function barbero()
    {
        return $this->belongsTo(Barbero::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'cita_servicio');
    }
}
