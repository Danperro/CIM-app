<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class periferico extends Model
{
    protected $table = 'periferico';

    protected $fillable = [
        'IdPef',
        'IdTpf',
        'CiuPef',
        'CodigoInventarioPef',
        'MarcaPef',
        'ColorPef',
        'EstadoPef'
    ];

    protected $primaryKey = 'IdPef';
    public $timestamps = true;

    // Relaciones
    public function tipoperiferico()
    {
        return $this->belongsTo(tipoperiferico::class, 'IdTpf', 'IdTpf');
    }

    // Relaciones futuras si usas detalleequipo
    // public function detalles()
    // {
    //     return $this->hasMany(detalleequipo::class, 'IdPef', 'IdPef');
    // }

    // public function scopeActivos($query)
    // {
    //     return $query->where('EstadoPef', 1);
    // }
}
