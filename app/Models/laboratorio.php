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

    public function scopeSearch($query, $search, $idAre = null)
    {
        if (!empty($search)) {
            $query->where('NombreLab', 'LIKE', '%' . trim($search) . '%');
        }
        if (!empty($idAre)) {
            $query->where('IdAre', $idAre);
        }
        return $query;
    }
}
