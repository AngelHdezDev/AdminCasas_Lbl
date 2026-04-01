<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'tipo',
        'titulo',
        'descripcion',
        'icono'
    ];
}
