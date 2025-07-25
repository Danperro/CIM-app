<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class usuario extends Authenticatable
{
    protected $table = 'usuario';

    protected $fillable = [
        'IdUsa',
        'IdRol',
        'IdPer',
        'UsernameUsa',
        'PasswordUsa',
        'EstadoUsa'
    ];

    protected $primaryKey = 'IdUsa';
    public $timestamps = true;
    use Notifiable;

    public function getAuthPassword()
    {
        return $this->PasswordUsa;
    }

    public function getAuthIdentifierName()
    {
        return 'UsernameUsa';
    }

    public function scopeSearch($query, $search)
    {
        if ($search == null) {
            return $query;
        }
        return $query->where('NombreUsa', 'LIKE', '%' . trim($search) . '%');
    }
    // Relaciones
    public function rol()
    {
        return $this->belongsTo(rol::class, 'IdRol', 'IdRol');
    }

    public function persona()
    {
        return $this->belongsTo(persona::class, 'IdPer', 'IdPer');
    }
    public function detalleusuario()
    {
        return $this->hasOne(detalleusuario::class, 'IdUsa', 'IdUsa');
    }
}
