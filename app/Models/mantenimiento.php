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

    public function incidencias()
    {
        return $this->hasMany(incidencia::class, 'IdMan', 'IdMan');
    }

    public function scopeSearch($query, $search, $idTpm = null, $idClm = null)
    {
        if (!empty($idTpm)) {
            $query->where('IdTpm', $idTpm);
        }
        if (!empty($idClm)) {
            $query->where('IdClm', $idClm);
        }
        if (!empty($search)) {
            $S = mb_strtolower(trim($search), 'UTF-8');
            $query->where('NombreMan', 'LIKE', '%' . trim($search) . '%');
        }
        return $query;
    }
}
