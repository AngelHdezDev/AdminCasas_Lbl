<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre', 
        'correo', 
        'contra',
    ];

  
    public function getAuthPassword()
    {
        return $this->contra;
    }

    public $timestamps = false; 
}