<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class rol extends Model
{
    protected $table = 'rol';

    protected $fillable = [
        'IdRol',
        'NombreRol',
        'EstadoRol'
    ];

    protected $primaryKey = 'IdRol';
    public $timestamps = true;

    // Relaciones
    public function usuarios()
    {
        return $this->hasMany(usuario::class, 'IdRol', 'IdRol');
    }

    // public function scopeActivos($query)
    // {
    //     return $query->where('EstadoRol', 1);
    // }
}
