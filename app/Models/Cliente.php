<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = ['nombre', 'telefono', 'correo', 'sucursal_id'];

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(sucursales::class, 'sucursal_id');
    }
}
