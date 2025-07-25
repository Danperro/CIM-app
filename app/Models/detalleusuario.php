<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class detalleusuario extends Model
{
    protected $table = 'detalleusuario';

    protected $fillable = [
        'IdDtu',
        'IdUsa',
        'IdLab',
        'EstadoDtu'
    ];

    protected $primaryKey = 'IdDtu';
    public $timestamps = true;

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(usuario::class, 'IdUsa', 'IdUsa');
    }

    public function laboratorio()
    {
        return $this->belongsTo(laboratorio::class, 'IdLab', 'IdLab');
    }

    // public function scopeActivos($query)
    // {
    //     return $query->where('EstadoDtu', 1);
    // }
}
