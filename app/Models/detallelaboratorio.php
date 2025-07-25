<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class detallelaboratorio extends Model
{
    protected $table = 'detallelaboratorio';

    protected $fillable = [
        'IdDtl',
        'IdLab',
        'ObservaciónDtl',
        'RealizadoDtl',
        'VerificadoDtl',
        'FechaDtl',
        'EstadoDtl'
    ];

    protected $primaryKey = 'IdDtl';
    public $timestamps = true;

    // Relaciones
    public function laboratorio()
    {
        return $this->belongsTo(laboratorio::class, 'IdLab', 'IdLab');
    }

    // public function scopeActivos($query)
    // {
    //     return $query->where('EstadoDtl', 1);
    // }
}
