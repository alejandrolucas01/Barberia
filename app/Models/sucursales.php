<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class sucursales extends Model
{
    protected $fillable = ['nombre', 'direccion', 'telefono', 'dias_apertura', 'hora_apertura', 'hora_cierre'];

    protected function casts(): array
    {
        return [
            'dias_apertura' => 'array',
        ];
    }

    public function secretarias()
    {
        return $this->hasMany(User::class, 'sucursal_id')->where('role', 'secretaria');
    }

    public function barberos()
    {
        return $this->hasMany(Barbero::class, 'sucursal_id');
    }
}
