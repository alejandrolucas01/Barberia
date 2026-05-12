<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $fillable = ['barbero_id', 'fecha', 'dia_semana', 'hora_inicio', 'hora_fin'];

    public function barbero()
    {
        return $this->belongsTo(Barbero::class, 'barbero_id');
    }
}
