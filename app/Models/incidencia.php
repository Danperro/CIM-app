<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class incidencia extends Model
{
    protected $table = 'incidencia';
    protected $primaryKey = 'IdInc';
    public $timestamps = true;

    protected $fillable = [
        'IdInc',
        'IdMan',
        'IdTpf',
        'NombreInc',
        'EstadoInc',
    ];

    protected $casts = [
        'EstadoInc' => 'boolean',
    ];

    /* =========================
       Relaciones
       ========================= */

    // Incidencia fue cerrada por un mantenimiento (opcional si decides permitir null)
    public function mantenimiento()
    {
        return $this->belongsTo(mantenimiento::class, 'IdMan', 'IdMan');
    }

    public function tipoperiferico()
    {
        return $this->belongsTo(tipoperiferico::class, 'IdTpf', 'IdTpf');
    }

    // Si usas la tabla incidencia-periférico, te dejo el gancho listo
    public function incidenciasPeriferico()
    {
        return $this->hasMany(incidenciaperiferico::class, 'IdInc', 'IdInc');
    }

    /* =========================
       Scopes de conveniencia
       ========================= */

    // Búsqueda tipo a la de tu modelo area
    public function scopeSearch($query, $search = null, $idMan = null,$idTpf=null)
    {
        if (!empty($search)) {
            $query->where('NombreInc', 'LIKE', '%' . trim($search) . '%');
        }
        if (!empty($idMan)) {
            $query->where('IdMan', $idMan);
        }
        if (!empty($idTpf)) {
            $query->where('IdTpf', $idTpf);
        }
        return $query;
    }
}
