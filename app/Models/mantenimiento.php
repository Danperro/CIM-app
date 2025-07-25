<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mantenimiento extends Model
{
    protected $table = 'mantenimiento';

    protected $fillable = [
        'IdMan',
        'IdTpm',
        'IdClm',
        'NombreMan',
        'EstadoMan'
    ];

    protected $primaryKey = 'IdMan';
    public $timestamps = true;

    // Relaciones
    public function tipomantenimiento()
    {
        return $this->belongsTo(tipomantenimiento::class, 'IdTpm', 'IdTpm');
    }

    public function clasemantenimiento()
    {
        return $this->belongsTo(clasemantenimiento::class, 'IdClm', 'IdClm');
    }

    // Relaciones futuras
    // public function detalles()
    // {
    //     return $this->hasMany(detallemantenimiento::class, 'IdMan', 'IdMan');
    // }

    // public function scopeActivos($query)
    // {
    //     return $query->where('EstadoMan', 1);
    // }
}
