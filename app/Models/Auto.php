<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auto extends Model
{
    protected $table = 'autos';
    protected $primaryKey = 'id_auto';

    protected $fillable = [
        'id_marca',
        'modelo',
        'tipo',
        'year',
        'color',
        'kilometraje',
        'precio',
        'transmision',
        'combustible',
        'created_at',
        'created_by',
        'descripcion',
        'ocultar_kilometraje',
        'consignacion',
        'active'
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'id_marca');
    }

    public function imagenes()
    {
        return $this->hasMany(Imagen::class, 'id_auto');
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    public function thumbnail()
    {
        // Le decimos: "Busca en la tabla imagenes una sola fila donde thumbnail sea 1"
        return $this->hasOne(Imagen::class, 'id_auto')->where('thumbnail', 1);
    }

    public $timestamps = false;
}