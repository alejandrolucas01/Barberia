<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $fillable = [
        'barbero_id',
        'cliente_id',
        'servicio_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'tipo_atencion',
        'estado'
    ];

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
}
