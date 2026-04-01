<?php

namespace App\Observers;

use App\Models\Auto;
use App\Models\ActivityLog;

class AutoObserver
{
    /**
     * Handle the Auto "created" event.
     */
    public function created(Auto $auto): void
    {
        ActivityLog::create([
            'tipo' => 'vehiculo',
            'titulo' => 'Nuevo vehículo registrado',
            'descripcion' => "{$auto->marca->nombre} {$auto->modelo} {$auto->year} • $" . number_format($auto->precio, 2),
            'icono' => 'bi bi-car-front-fill'
        ]);
    }

    /**
     * Handle the Auto "updated" event.
     */
    public function updated(Auto $auto): void
    {
        if ($auto->isDirty('precio')) {
            $precioAnterior = number_format($auto->getOriginal('precio'));
            $precioNuevo = number_format($auto->precio);

            ActivityLog::create([
                'tipo' => 'precio',
                'titulo' => 'Precio actualizado',
                'descripcion' => "{$auto->marca->nombre} {$auto->modelo} • Nuevo precio: ${precioNuevo} (Antes: ${precioAnterior})",
                'icono' => 'bi-tags-fill'
            ]);
        }
        if ($auto->isDirty('active')) {
            ActivityLog::create([
                'tipo' => 'marca',
                'titulo' => 'Vehículo eliminado',
                'descripcion' => "Se ha eliminado el vehículo '{$auto->marca->nombre} {$auto->modelo}'.",
                'icono' => 'bi-trash3-fill'
            ]);
        }
    }

    /**
     * Handle the Auto "deleted" event.
     */
    public function deleted(Auto $auto): void
    {
        dd("El vehículo {$auto->marca->nombre} {$auto->modelo} ha sido eliminado.");
        ActivityLog::create([
            'tipo' => 'vehiculo',
            'titulo' => 'Vehículo eliminado',
            'descripcion' => "Se retiró  {$auto->modelo} del inventario.",
            'icono' => 'bi-trash-fill'
        ]);
    }
    /**
     * Handle the Auto "restored" event.
     */
    public function restored(Auto $auto): void
    {
        //
    }

    /**
     * Handle the Auto "force deleted" event.
     */
    public function forceDeleted(Auto $auto): void
    {
        //
    }
}
