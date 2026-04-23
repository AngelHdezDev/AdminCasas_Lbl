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
        $property = Property::findOrFail($id);
        $this->service->updateProperty($property, $request->validated());
        $property->amenities()->sync($request->input('amenities', []));

        return redirect()->route('propiedades.index')
            ->with('success', 'Propiedad actualizada con éxito');
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
}
