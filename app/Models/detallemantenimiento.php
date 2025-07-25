<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class detallemantenimiento extends Model
{
    protected $table = 'detallemantenimiento';

    protected $fillable = [
        'IdDtm',
        'IdMan',
        'IdEqo',
        'FechaDtm',
        'EstadoDtm'
    ];

    protected $primaryKey = 'IdDtm';
    public $timestamps = true;

    // Relaciones
    public function mantenimiento()
    {
        return $this->belongsTo(mantenimiento::class, 'IdMan', 'IdMan');
    }

    public function equipo()
    {
        return $this->belongsTo(equipo::class, 'IdEqo', 'IdEqo');
    }

    // public function scopeActivos($query)
    // {
    //     return $query->where('EstadoDtm', 1);
    // }
}
