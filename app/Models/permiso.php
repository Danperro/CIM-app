<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class permiso extends Model
{
    protected $table = 'permiso';

    protected $fillable = [
        'IdPem',
        'NombrePem',
        'EstadoPem'
    ];

    protected $primaryKey = 'IdPem';
    public $timestamps = true;

    // Relaciones futuras (por ejemplo, roles que tienen permisos)
    // public function roles()
    // {
    //     return $this->belongsToMany(rol::class, 'rol_permiso', 'IdPem', 'IdRol');
    // }

    // public function scopeActivos($query)
    // {
    //     return $query->where('EstadoPem', 1);
    // }
}
