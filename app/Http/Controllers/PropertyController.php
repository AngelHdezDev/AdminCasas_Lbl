<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Marca;
use App\Models\Property;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Services\PropertyService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Marca\StoreMarcaRequest;
use App\Http\Requests\Marca\UpdateMarcaRequest;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use App\Models\Seller;
use App\Models\Client;
use App\Models\Amenity;
use Illuminate\Support\Facades\Http;
use App\Models\PropertyImage;
use Illuminate\Support\Facades\Storage;
;


class PropertyController extends Controller
{
    protected $service;

    public function __construct(PropertyService $service)
    {
        $this->service = $service;
    }


    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $property = $this->service->createProperty($request->validated());

        if ($request->has('amenities')) {
            $property->amenities()->sync($request->amenities);
        }

        return redirect()->route('propiedades.index')
            ->with('success', 'Propiedad registrada con éxito');
    }

    public function index(Request $request)
    {
        $properties = $this->service->getAllPaginated(10, $request->all());
        $vendedores = Seller::orderBy('name', 'asc')->get();
        $clientes = Client::orderBy('name', 'asc')->get();
        $states = Property::whereNotNull('state')
            ->distinct()
            ->orderBy('state', 'asc')
            ->pluck('state');

        return view('autos.autos', compact('properties', 'vendedores', 'clientes', 'states'));
    }
    public function update(UpdatePropertyRequest $request, $id): RedirectResponse
    {
        try {
            $property = Property::findOrFail($id);

            // 1. Validar el límite de destacados antes de procesar el servicio
            if ($request->input('is_featured', 0) == 1) {
                $totalDestacados = Property::where('is_featured', 1)
                    ->where('id', '!=', $id)
                    ->count();

                if ($totalDestacados >= 6) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error_destacados', 'No se puede destacar esta propiedad. Ya alcanzaste el límite máximo de 6 propiedades destacadas.');
                }
            }

            // 2. Intentar guardar los cambios en la base de datos
            $this->service->updateProperty($property, $request->validated());
            $property->amenities()->sync($request->input('amenities', []));

            return redirect()->route('propiedades.index')
                ->with('success', 'Propiedad actualizada con éxito');

        } catch (\Exception $e) {
            // Loggeamos el error real por detrás por si necesitas revisarlo en storage/logs/laravel.log
            \Log::error("Error al actualizar la propiedad ID {$id}: " . $e->getMessage());

            // Regresamos al usuario avisando que algo salió mal
            return redirect()->back()
                ->withInput()
                ->with('error_sistema', 'Ocurrió un error inesperado al guardar los cambios: ' . $e->getMessage());
        }
    }
    public function showDetail($id_property)
    {
        $property = Property::with(['client', 'seller', 'amenities'])->findOrFail($id_property);

        return view('autos.autosDetail', compact('property'));
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);

        $property->update([
            'active' => false
        ]);

        return redirect()->route('propiedades.index')
            ->with('success', 'La propiedad ha sido dada de baja correctamente.');
    }

    public function create()
    {
        $vendedores = Seller::orderBy('name', 'asc')->get();
        $clientes = Client::orderBy('name', 'asc')->get();
        $amenities = Amenity::all();
        return view('autos.addPropiedad', compact('vendedores', 'clientes', 'amenities'));
    }

    public function autocomplete(Request $request)
    {
        $query = $request->get('q');
        $response = Http::withHeaders([
            'User-Agent' => 'AdminCasas/1.0'
        ])->get("https://nominatim.openstreetmap.org/search", [
                    'q' => $query,
                    'format' => 'json',
                    'addressdetails' => 1,
                    'limit' => 5
                ]);

        return response()->json($response->json());
    }

    public function edit($id)
    {
        $property = Property::findOrFail($id);
        $vendedores = Seller::orderBy('name', 'asc')->get();
        $clientes = Client::orderBy('name', 'asc')->get();
        $amenities = Amenity::all();
        return view('autos.editPropiedad', compact('property', 'vendedores', 'clientes', 'amenities'));
    }

    public function eliminarImagen($id)
    {
        $imagen = PropertyImage::findOrFail($id);

        // Eliminar archivo y registro...
        if (Storage::exists($imagen->path)) {
            Storage::delete($imagen->path);
        }
        $imagen->delete();

        // ESTO ES LO QUE EL JS NECESITA LEER:
        return response()->json([
            'success' => true,
            'message' => 'Imagen eliminada correctamente.'
        ], 200);
    }

    public function toggleDestacado(Request $request, $id)
    {
        try {
            $propiedad = Property::findOrFail($id);

            // Asumiendo que tu columna se llama 'is_featured' (cambialo si es 'destacado', etc.)
            $columna = 'is_featured';

            // Si el usuario la quiere marcar como destacada (actualmente está en 0)
            if ($propiedad->$columna == 0) {
                // Contamos cuántas propiedades ya están destacadas en total
                $totalDestacados = Property::where($columna, 1)->count();

                if ($totalDestacados >= 6) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Límite alcanzado. Solo se permiten 6 propiedades destacadas a la vez.'
                    ], 422);
                }

                $propiedad->$columna = 1;
                $mensaje = 'Propiedad marcada como destacada.';
            } else {
                // Si ya estaba destacada, simplemente la desmarcamos (pasar de 1 a 0)
                $propiedad->$columna = 0;
                $mensaje = 'Propiedad quitada de destacados.';
            }

            $propiedad->save();

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'is_featured' => $propiedad->$columna
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }
}
