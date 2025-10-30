<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;

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

    public function scopeSearch($query, $search, $idRol)
    {
        $query->when($search, function ($query) use ($search) {
            $s = mb_strtolower(trim($search), 'UTF-8');
            $query->where(function ($querylimpio) use ($s) {
                $querylimpio->whereRaw('LOWER(`UsernameUsa`) LIKE ?', ["%{$s}%"])
                    // Relación persona: nombre y apellidos
                    ->orWhereHas('persona', function (Builder $p) use ($s) {
                        $p->whereRaw('LOWER(`NombrePer`) LIKE ?', ["%{$s}%"])
                            ->orWhereRaw('LOWER(`ApellidoPaternoPer`) LIKE ?', ["%{$s}%"])
                            ->orWhereRaw('LOWER(`ApellidoMaternoPer`) LIKE ?', ["%{$s}%"]);
                    });
            });
        });
        $query->when($idRol, fn($query) => $query->where('IdRol', $idRol));

        return $query;
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
        return $this->hasMany(detalleusuario::class, 'IdUsa', 'IdUsa');
    }
}
