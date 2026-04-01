<?php

namespace App\Http\Controllers;

use App\Models\ImagenTemporal;
use App\Models\Imagen;
use App\Models\Auto;
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
        $vehiculos = Auto::where('active', 1)->get();

        return view('galeria.galeria', compact('imagenes', 'vehiculos'));
    }

    public function asignar(Request $request, $id)
    {
        $request->validate([
            'id_auto' => 'required|exists:autos,id_auto'
        ]);

        try {
            $temp = ImagenTemporal::findOrFail($id);

            DB::transaction(function () use ($temp, $request) {

                Imagen::create([
                    'id_auto' => $request->id_auto,
                    'imagen' => $temp->ruta_archivo,
                    'thumbnail' => 0,
                    'created_by' => auth()->id() ?? 1
                ]);


                $temp->update(['status' => 1]);
            });

            return redirect()->back()->with('success', 'Asignado correctamente');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al asignar: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $temp = ImagenTemporal::findOrFail($id);

            if (Storage::disk('public')->exists($temp->ruta_archivo)) {
                Storage::disk('public')->delete($temp->ruta_archivo);
            }

            $temp->delete();

            return redirect()->back()->with('success', 'Imagen eliminada correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
    public function setPortada($id)
    {
        try {
            $imagen = Imagen::findOrFail($id);

            Imagen::where('id_auto', $imagen->id_auto)
                ->update(['thumbnail' => false]);

            $imagen->update(['thumbnail' => true]);

            return back()->with('success', 'Portada actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo actualizar la portada.');
        }
    }
}
