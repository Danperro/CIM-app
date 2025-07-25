<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class clasemantenimiento extends Model
{
    protected $table = 'clasemantenimiento';

    protected $fillable = [
        'IdClm',
        'NombreClm',
        'EstadoClm'
    ];

    protected $primaryKey = 'IdClm';
    public $timestamps = true;

    // Relaciones futuras
    // public function mantenimientos()
    // {
    //     return $this->hasMany(mantenimiento::class, 'IdClm', 'IdClm');
    // }

    // public function scopeActivos($query)
    // {
    //     return $query->where('EstadoClm', 1);
    // }
}
