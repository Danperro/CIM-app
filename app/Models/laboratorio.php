<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class laboratorio extends Model
{
    protected $table = 'laboratorio';

    protected $fillable = [
        'IdLab',
        'IdAre',
        'NombreLab',
        'EstadoLab'
    ];

    protected $primaryKey = 'IdLab';
    public $timestamps = true;

    // Relaciones
    public function area()
    {
        return $this->belongsTo(area::class, 'IdAre', 'IdAre');
    }

    // public function scopeActivos($query)
    // {
    //     return $query->where('EstadoLab', 1);
    // }
}
