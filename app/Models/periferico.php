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
    public function equipo()
    {
        return $this->belongsTo(equipo::class, 'IdEqo', 'IdEqo');
    }
    public function detalleequipo()
    {
        return $this->hasOne(detalleequipo::class, 'IdPef', 'IdPef');
    }


    public function detalles()
    {
        return $this->hasMany(detalleEquipo::class, 'IdPef', 'IdPef');
    }
    public function equipos()
    {
        return $this->belongsToMany(
            equipo::class,
            'detalleequipo',   // tabla pivote
            'IdPef',           // FK a periférico en pivote
            'IdEqo'            // FK a equipo en pivote
        );
    }
    // Scope para buscar periférico por código de inventario
    public function scopeByInventario($query, string $codigo)
    {
        return $query->where('CodigoInventarioPef', trim($codigo));
    }

    public function scopeSearch($query, $search = null, $idTpf = null, $ciuPef = null, $marcaPef = null, $colorPef = null, $estadoPef = null)
    {
        // Filtros exactos
        if (!empty($idTpf)) {
            $query->where('IdTpf', $idTpf);
        }

        if (!empty($ciuPef)) {
            $query->where('CiuPef', 'LIKE', '%' . trim($ciuPef) . '%');
        }

        if (!empty($marcaPef)) {
            $query->where('MarcaPef', 'LIKE', '%' . trim($marcaPef) . '%');
        }

        if (!empty($colorPef)) {
            $query->where('ColorPef', 'LIKE', '%' . trim($colorPef) . '%');
        }
        if ($estadoPef !== null && $estadoPef != '') {
            $query->where('EstadoPef',(int) $estadoPef);
        }
        // Búsqueda libre
        if (!empty($search)) {
            $S = trim(mb_strtolower($search, 'UTF-8'));
            $query->where(function ($q) use ($S) {
                $q->whereRaw('LOWER(CodigoInventarioPef) LIKE ?', ["%{$S}%"])
                    ->orWhereRaw('LOWER(MarcaPef) LIKE ?', ["%{$S}%"])
                    ->orWhereRaw('LOWER(ColorPef) LIKE ?', ["%{$S}%"])
                    ->orWhereRaw('LOWER(CiuPef) LIKE ?', ["%{$S}%"]);
            });
        }

        return $query;
    }
}
