<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class incidenciaperiferico extends Model
{
    protected $table = 'incidenciaperiferico';
    protected $primaryKey = 'IdIpf';
    public $timestamps = true;

    protected $fillable = [
        'IdIpf',
        'IdInc',
        'IdPef',
        'FechaIpf',
        'EstadoIpf',
    ];

    protected $casts = [
        'EstadoIpf' => 'boolean',
        'FechaIpf' => 'date',
    ];

    /* =========================
       Relaciones
       ========================= */

    public function incidencia()
    {
        return $this->belongsTo(incidencia::class, 'IdInc', 'IdInc');
    }

    public function periferico()
    {
        return $this->belongsTo(periferico::class, 'IdPef', 'IdPef');
    }

    /* =========================
       Scopes de conveniencia
       ========================= */

    public function scopeActivos($query)
    {
        return $query->where('EstadoIpf', 1);
    }

    public function scopeInactivos($query)
    {
        return $query->where('EstadoIpf', 0);
    }
}
