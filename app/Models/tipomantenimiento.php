<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tipomantenimiento extends Model
{
    protected $table = 'tipomantenimiento';

    protected $fillable = [
        'IdTpm',
        'NombreTpm',
        'EstadoTpm'
    ];

    protected $primaryKey = 'IdTpm';
    public $timestamps = true;

    // Relaciones futuras (si los mantenimientos hacen referencia al tipo)
    // public function mantenimientos()
    // {
    //     return $this->hasMany(mantenimiento::class, 'IdTpm', 'IdTpm');
    // }

    // public function scopeActivos($query)
    // {
    //     return $query->where('EstadoTpm', 1);
    // }
}
