<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class detalleequipo extends Model
{
    protected $table = 'detalleequipo';

    protected $fillable = [
        'IdDte',
        'IdEqo',
        'IdPef',
        'EstadoDte'
    ];

    protected $primaryKey = 'IdDte';
    public $timestamps = true;

    // Relaciones
    public function equipo()
    {
        return $this->belongsTo(equipo::class, 'IdEqo', 'IdEqo');
    }

    public function periferico()
    {
        
        return $this->belongsTo(periferico::class, 'IdPef', 'IdPef');
    }

}
