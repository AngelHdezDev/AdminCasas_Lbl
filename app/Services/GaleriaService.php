<?php

namespace App\Services;

use App\Models\PropertyImage;
use App\Models\Property;
use Illuminate\Support\Facades\Storage;

class GaleriaService
{
    /**
     * Obtiene las imágenes paginadas que aún no tienen propiedad asignada.
     */
    public function getUnassignedPaginated($perPage = 15)
    {
        return PropertyImage::whereNull('property_id')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Procesa la subida física y el registro en BD de múltiples imágenes.
     */
    public function uploadMultiple(array $files)
    {
        $uploadedImages = [];

        foreach ($files as $file) {
            // Guardamos en storage/app/public/properties con nombre único
            $path = $file->store('properties', 'public');

            // Creamos el registro en la base de datos
            $uploadedImages[] = PropertyImage::create([
                'property_id' => null, // Nacen sin dueño
                'path' => $path,
                'is_main' => false,
                'is_hero' => false
            ]);
        }

        return $uploadedImages;
    }

    /**
     * Asigna una imagen existente a una propiedad específica.
     */
    public function assignToProperty($imageId, $propertyId)
    {
        $image = PropertyImage::findOrFail($imageId);

        // Si propertyId llega vacío desde el select, se guarda como null
        return $image->update([
            'property_id' => $propertyId ?: null
        ]);
    }

    /**
     * Elimina el archivo físico y el registro en la base de datos.
     */
    public function deleteImage($id)
    {
        $image = PropertyImage::findOrFail($id);
        if (Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }
        return $image->delete();
    }

    public function asignar(Request $request, $id)
    {
        // Validamos que el ID de propiedad exista (o sea nulo si se quiere desasignar)
        $request->validate([
            'property_id' => 'nullable|exists:properties,id'
        ]);

        try {
            // Llamamos al service para que haga el update
            $this->galeriaService->assignToProperty($id, $request->property_id);

            return redirect()->back()->with('success', 'Imagen asignada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se pudo asignar la imagen.');
        }
    }
}