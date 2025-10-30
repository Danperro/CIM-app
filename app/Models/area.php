<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class area extends Model
{
    protected $table = 'area';

    protected $fillable = [
        'IdAre',
        'NombreAre',
        'EstadoAre'
    ];

    protected $primaryKey = 'IdAre';
    public $timestamps = true;

    // Relaciones futuras
    // public function laboratorios()
    // {
    //     return $this->hasMany(laboratorio::class, 'IdAre', 'IdAre');
    // }

    public function scopeSearch($query, $search, $idAre)
    {
        if (!empty($search)) {
            $query->where('NombreAre', 'LIKE', '%' . trim($search) . '%');
        }
        if (!empty($idAre)) {
            $query->where('IdAre', $idAre);
        }
        return $query;
    }
}
