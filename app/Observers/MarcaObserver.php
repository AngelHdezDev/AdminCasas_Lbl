<?php

namespace App\Observers;

use App\Models\Marca;
use App\Models\ActivityLog;

class MarcaObserver
{
    /**
     * Handle the Marca "created" event.
     */
    public function created(Marca $marca): void
    {
        ActivityLog::create([
            'tipo' => 'marca',
            'titulo' => 'Nueva marca registrada',
            'descripcion' => "Se ha añadido '{$marca->nombre}' al catálogo de marcas.",
            'icono' => 'bi-patch-check-fill'
        ]);
    }

    /**
     * Handle the Marca "updated" event.
     */
    public function updated(Marca $marca): void
    {

        if ($marca->isDirty('nombre')) {
            $nombreViejo = $marca->getOriginal('nombre');
            $nombreNuevo = $marca->nombre;

            ActivityLog::create([
                'tipo' => 'marca',
                'titulo' => 'Marca actualizada',
                'descripcion' => "Se cambió el nombre la marca de '{$nombreViejo}' a '{$nombreNuevo}'.",
                'icono' => 'bi-pencil-square'
            ]);
        }


        if ($marca->isDirty('imagen')) {
            ActivityLog::create([
                'tipo' => 'marca',
                'titulo' => 'Logo actualizado',
                'descripcion' => "Se ha actualizado el logo de la marca '{$marca->nombre}'.",
                'icono' => 'bi-image'
            ]);
        }

        if ($marca->isDirty('active')) {
            $estado = $marca->active == 1 ? 'activado' : 'desactivado';
            $titulo = $marca->active == 1 ? 'Marca reactivada' : 'Marca desactivada';
            $icono = $marca->active == 1 ? 'bi-check-circle-fill' : 'bi-x-circle-fill';

            ActivityLog::create([
                'tipo' => 'marca',
                'titulo' => $titulo,
                'descripcion' => "Se ha {$estado} la marca '{$marca->nombre}' correctamente.",
                'icono' => $icono
            ]);
        }
    }

    /**
     * Handle the Marca "deleted" event.
     */
    public function deleted(Marca $marca): void
    {
        ActivityLog::create([
            'tipo' => 'marca',
            'titulo' => 'Marca desactivada',
            'descripcion' => "Se ha desactivado la marca '{$marca->nombre}' y sus registros asociados.",
            'icono' => 'bi-trash3-fill'
        ]);
    }

    /**
     * Handle the Marca "restored" event.
     */
    public function restored(Marca $marca): void
    {
        //
    }

    /**
     * Handle the Marca "force deleted" event.
     */
    public function forceDeleted(Marca $marca): void
    {
        //
    }
}
