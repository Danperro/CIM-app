<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class menu extends Model
{
    protected $table = 'menu';

    protected $fillable = [
        'IdMen',
        'NombreMen',
        'EstadoMen'
    ];

    protected $primaryKey = 'IdMen';
    public $timestamps = true;

    // Relaciones futuras (si hay, por ejemplo con accesos, submenús, etc.)
    // public function accesos()
    // {
    //     return $this->hasMany(acceso::class, 'IdMen', 'IdMen');
    // }

    // public function scopeActivos($query)
    // {
    //     return $query->where('EstadoMen', 1);
    // }
}
