<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagenTemporal extends Model
{
    protected $fillable = [
        'ruta_archivo',
        'nombre_original',
        'correo_origen',
        'asunto',
        'fecha_correo',
        'status'
    ];
}
