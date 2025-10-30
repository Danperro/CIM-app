<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class detallelaboratorio extends Model
{
    protected $table = 'detallelaboratorio';

    protected $fillable = [
        'IdDtl',
        'IdLab',
        'RealizadoDtl',
        'IdTpm',
        'FechaDtl',
        'EstadoDtl'
    ];

    protected $primaryKey = 'IdDtl';
    public $timestamps = true;

    // Relaciones
    public function equipo()
    {
        return $this->belongsTo(equipo::class, 'IdEqo', 'IdEqo');
    }

    public function laboratorio()
    {
        return $this->belongsTo(laboratorio::class, 'IdLab', 'IdLab');
    }
    public function tipomantenimiento()
    {
        return $this->belongsTo(tipomantenimiento::class, 'IdTpm', 'IdTpm');
    }

    public function scopeSearch(Builder $q, $idLab = null, $fecha = null, $usuario = null)
    {
        return $q
            ->when($idLab, fn($qq) => $qq->where('IdLab', $idLab))
            ->when($fecha, fn($qq) => $qq->whereDate('FechaDtl', $fecha))
            ->when($usuario, function ($qq) use ($usuario) {
                $s = trim($usuario);
                $qq->where(function ($w) use ($s) {
                    $w->where('RealizadoDtl', 'like', "%{$s}%");
                });
            });
    }
}
