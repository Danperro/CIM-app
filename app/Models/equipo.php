<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class equipo extends Model
{
    protected $table = 'equipo';

    protected $fillable = [
        'IdEqo',
        'IdLab',
        'NombreEqo',
        'CodigoEqo',
        'EstadoEqo'
    ];

    protected $primaryKey = 'IdEqo';
    public $timestamps = true;

    // Relaciones
    public function laboratorio()
    {
        return $this->belongsTo(laboratorio::class, 'IdLab', 'IdLab');
    }

    public function scopeSearch($query, $search)
    {
        if ($search == null) {
            return $query;
        }
        return $query->where('NombreEqo', 'LIKE', '%' . trim($search) . '%')
            ->orWhere('CodigoEqo', 'LIKE', '%' . trim($search) . '%');
    }


    // Relaciones futuras
    // public function detalles()
    // {
    //     return $this->hasMany(detalleequipo::class, 'IdEqo', 'IdEqo');
    // }
}
