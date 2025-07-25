<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class persona extends Model
{
    protected $table = 'persona';

    protected $fillable = [
        'IdPer',
        'NombrePer',
        'ApellidoPaternoPer',
        'ApellidoMaternoPer',
        'FechaNacimientoPer',
        'DniPer',
        'TelefonoPer',
        'CorreoPer',
        'EstadoPer'
    ];

    protected $primaryKey = 'IdPer';
    public $timestamps = true;
}
