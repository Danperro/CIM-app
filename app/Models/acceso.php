<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class acceso extends Model
{
    protected $table = 'acceso';

    protected $fillable = [
        'IdAcs',
        'IdRol',
        'IdPem',
        'IdMen'
    ];

    protected $primaryKey = 'IdAcs';
    public $timestamps = true;

    // Relaciones
    public function rol()
    {
        return $this->belongsTo(rol::class, 'IdRol', 'IdRol');
    }

    public function permiso()
    {
        return $this->belongsTo(permiso::class, 'IdPem', 'IdPem');
    }

    public function menu()
    {
        return $this->belongsTo(menu::class, 'IdMen', 'IdMen');
    }

    // public function scopePorRol($query, $rolId)
    // {
    //     return $query->where('IdRol', $rolId);
    // }
}
