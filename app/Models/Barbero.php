<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barbero extends Model
{
    protected $fillable = ['nombre', 'telefono', 'correo', 'hora_entrada', 'hora_salida', 'sucursal_id'];

    public function sucursal()
    {
        return $this->belongsTo(sucursales::class, 'sucursal_id');
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'barbero_id');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'barbero_id');
    }
}
