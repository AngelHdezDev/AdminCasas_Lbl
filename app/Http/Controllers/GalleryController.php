<?php

namespace App\Http\Controllers;

use App\Models\ImagenTemporal;
use App\Models\PropertyImage; // Tu nuevo modelo
use App\Models\Property;      // Tu modelo de casas
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $imagenes = ImagenTemporal::where('status', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $properties = Property::where('active', 1)->orderBy('title', 'asc')->get(); 

        return view('galeria.galeria', compact('imagenes', 'properties'));
    }

    public function asignar(Request $request, $id)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id'
        ]);

        try {
            $temp = ImagenTemporal::findOrFail($id);

            DB::transaction(function () use ($temp, $request) {
                // Verificamos si ya hay una foto principal para esta casa
                $tienePrincipal = PropertyImage::where('property_id', $request->property_id)
                    ->where('is_main', 1)
                    ->exists();

                PropertyImage::create([
                    'property_id' => $request->property_id,
                    'path'        => $temp->ruta_archivo, // Usamos 'path' como en tu DBeaver
                    'is_main'     => $tienePrincipal ? 0 : 1, // Si es la primera, es la principal
                    'is_hero'     => 0,
                    'created_at'  => now()
                ]);

                // Marcamos la temporal como procesada
                $temp->update(['status' => 1]);
            });

            return redirect()->back()->with('success', 'Imagen asignada a la propiedad correctamente');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al asignar: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $temp = ImagenTemporal::findOrFail($id);

            // Borramos el archivo físico del storage
            if (Storage::disk('public')->exists($temp->ruta_archivo)) {
                Storage::disk('public')->delete($temp->ruta_archivo);
            }

            $temp->delete();

            return redirect()->back()->with('success', 'Imagen eliminada de la bandeja temporal.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function setPortada($id)
    {
        try {
            // Buscamos en property_images
            $imagen = PropertyImage::findOrFail($id);

            DB::transaction(function () use ($imagen) {
                // Quitamos la principal de todas las fotos de esa propiedad
                PropertyImage::where('property_id', $imagen->property_id)
                    ->update(['is_main' => 0]);

                // Ponemos esta como principal
                $imagen->update(['is_main' => 1]);
            });

            return back()->with('success', 'Portada de la propiedad actualizada.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo actualizar la portada.');
        }
    }
}