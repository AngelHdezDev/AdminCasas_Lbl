<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    protected $table = 'imagenes';
    protected $primaryKey = 'id_imagen';

    protected $fillable = ['id_auto', 'imagen', 'thumbnail', 'created_by'];

    public function auto()
    {
        return $this->belongsTo(Auto::class, 'id_auto');
    }

    public $timestamps = false; 
}
