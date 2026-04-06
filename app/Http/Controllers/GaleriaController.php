<?php

namespace App\Http\Controllers;

use App\Services\GaleriaService;
use App\Models\Property; // Para listar propiedades en el select de asignación
use Illuminate\Http\Request;

class GaleriaController extends Controller
{
    protected $galeriaService;

    public function __construct(GaleriaService $galeriaService)
    {
        $this->galeriaService = $galeriaService;
    }

    public function index()
    {
        $imagenes = $this->galeriaService->getUnassignedPaginated(12);

        // También necesitamos las propiedades para el select del modal/galería
        $properties = Property::where('active', true)->get();

        return view('galeria.galeria', compact('imagenes', 'properties'));
    }

    public function store(Request $request)
    {
        if ($request->hasFile('imagenes')) {
            $this->galeriaService->uploadMultiple($request->file('imagenes'));
            return redirect()->back()->with('success', '¡Imágenes subidas con éxito!');
        }

        return redirect()->back()->with('error', 'No se seleccionaron imágenes.');
    }

    public function asignar(Request $request, $id)
    {
        // Usamos el ID de la imagen ($id) y el ID de la propiedad que viene en el select
        $this->galeriaService->assignToProperty($id, $request->property_id);

        return redirect()->back()->with('success', 'Imagen vinculada correctamente');
    }

    public function destroy($id)
    {
        try {
            $this->galeriaService->deleteImage($id);

            return redirect()->back()->with('success', 'Imagen eliminada permanentemente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se pudo eliminar la imagen: ' . $e->getMessage());
        }
    }
}