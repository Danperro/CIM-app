<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tipoperiferico extends Model
{
    protected $table = 'tipoperiferico';

    protected $fillable = [
        'IdTpf',
        'NombreTpf',
        'EstadoTpf'
    ];

    protected $primaryKey = 'IdTpf';
    public $timestamps = true;

    // Relaciones
    public function perifericos()
    {
        return $this->hasMany(periferico::class, 'IdTpf', 'IdTpf');
    }

    // public function scopeActivos($query)
    // {
    //     return $query->where('EstadoTpf', 1);
    // }
}
